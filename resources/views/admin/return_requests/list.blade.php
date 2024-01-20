@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Return Request Items</a></li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Return Request Items List</h4>

                        <div class="table-responsive">
                            <table id="ReturnRequest" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Image</th>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Payment Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Image</th>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Payment Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                    <div id="ordercoverspin" class="cover-spin"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ReturnReqVideoModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                </div>
                <div class="modal-body">
                    {{--<video width="400" controls>
                        <source src="" type="video/mp4" id="ReturnReqVideo">
                        Your browser does not support HTML video.
                    </video>--}}
                    <iframe id="ReturnReqVideo" class="embed-responsive-item" width="450" height="315" src="" allowfullscreen></iframe>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- return request JS start -->
<script type="text/javascript">
$(document).ready(function() {
    return_request_table(true);
});

function return_request_table(is_clearState=false){
    if(is_clearState){
        $('#ReturnRequest').DataTable().state.clear();
    }

    $('#ReturnRequest').DataTable({
        "destroy": true,
        "processing": true,
        "serverSide": true,
        'stateSave': function(){
            if(is_clearState){
                return false;
            }
            else{
                return true;
            }
        },
        "ajax":{
            "url": "{{ url('admin/allReturnRequestlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}'},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "20px", "targets": 0 },
            { "width": "50px", "targets": 1 },
            { "width": "70px", "targets": 2 },
            { "width": "230px", "targets": 3 },
            { "width": "150px", "targets": 4 },
            { "width": "100px", "targets": 5 },
            { "width": "100px", "targets": 6 },
            { "width": "200px", "targets": 7 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false ,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'image', name: 'image', orderable: false, class: "text-center"},
            {data: 'order_id', name: 'order_id', orderable: false, class: "text-center"},
            {data: 'product_info', name: 'product_info', orderable: false, class: "text-left multirow"},
            {data: 'price_info', name: 'price_info', orderable: false, class: "text-left multirow"},
            {data: 'payment_status', name: 'payment_status', orderable: false, class: "text-center multirow"},
            {data: 'created_at', name: 'created_at', orderable: false, class: "text-left multirow"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

function editOrder(orderId) {
    var url = "{{ url('admin/viewOrder') }}" + "/" + orderId;
    window.open(url,"_blank");
}

$('body').on('click', '#ApproveReturnRequestBtn', function () {
    $('#ordercoverspin').show();
    var item_id = $(this).attr('data-id');

    $.ajax ({
        type:"POST",
        url: '{{ url("admin/change_order_status") }}',
        data: {item_id: item_id, action: 'item_approve',  "_token": "{{csrf_token()}}"},
        success: function(res) {
            if(res['status'] == 200){
                toastr.success("Order Item Returned",'Success',{timeOut: 5000});
            } else {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        complete: function(){
            $('#ordercoverspin').hide();
            return_request_table();
        },
        error: function() {
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$('body').on('click', '#RejectReturnRequestBtn', function () {
    $('#ordercoverspin').show();
    var item_id = $(this).attr('data-id');

    $.ajax ({
        type:"POST",
        url: '{{ url("admin/change_order_status") }}',
        data: {item_id: item_id, action: 'item_reject',  "_token": "{{csrf_token()}}"},
        success: function(res) {
            if(res['status'] == 200){
                toastr.success("Order Item Delivered",'Success',{timeOut: 5000});
            } else {
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        complete: function(){
            $('#ordercoverspin').hide();
            return_request_table();
        },
        error: function() {
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$('body').on('click', '#VideoBtn', function () {
    var order_item_id = $(this).attr('data-id');
    $.get("{{ url('admin/return_requests') }}" +'/' + order_item_id +'/play_video', function (res) {
        $('#ReturnReqVideoModal').find('#ReturnReqVideo').attr('src',res['order_return_video']);
        // $('#ReturnReqVideoModal').find('#ReturnReqVideo').attr('type',res['type']);
    })
});

$('#ReturnReqVideoModal').on('hidden.bs.modal', function () {
    $(this).find("#ReturnReqVideo").attr('src','');
});
</script>
<!-- return request JS end -->
@endsection


