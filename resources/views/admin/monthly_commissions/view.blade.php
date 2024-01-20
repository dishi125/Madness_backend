@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Monthly Commission</a></li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Commission List</h4>

                        <div class="table-responsive">
                            <table id="Commission" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Order</th>
                                    <th>Commission</th>
                                    <th>Order By</th>
                                    <th>Order Status</th>
                                    <th>Date</th>
                                </tr>
                                </thead>

                                <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>Order</th>
                                    <th>Commission</th>
                                    <th>Order By</th>
                                    <th>Order Status</th>
                                    <th>Date</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="commissioncoverspin" class="cover-spin"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script type="text/javascript">
$(document).ready(function() {
    commission_table(true);
});

function commission_table(is_clearState=false){
    if(is_clearState){
        $('#Commission').DataTable().state.clear();
    }

    $('#Commission').DataTable({
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
            "url": "{{ url('admin/allCommissionlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}', id: '{{ $id }}'},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "20px", "targets": 0 },
            { "width": "120px", "targets": 1 },
            { "width": "100px", "targets": 2 },
            { "width": "150px", "targets": 3 },
            { "width": "150px", "targets": 4 },
            { "width": "120px", "targets": 5 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false ,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'order_info', name: 'order_info', orderable: false, class: "text-left multirow"},
            {data: 'commission_info', name: 'commission_info', orderable: false, class: "text-left multirow"},
            {data: 'order_by', name: 'order_by', class: "text-left", orderable: false},
            {data: 'order_status', name: 'order_status', orderable: false, class: "text-center"},
            {data: 'date', name: 'date', orderable: false, class: "text-left multirow"},
        ]
    });
}
</script>
@endsection
