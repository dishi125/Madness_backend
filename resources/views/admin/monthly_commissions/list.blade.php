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
                        <h4 class="card-title">Monthly Commission List</h4>

                        <div class="custom-tab-1">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item monthly_commission_tabs" data-tab="ALL_monthly_commission_tab"><a class="nav-link active show" data-toggle="tab" href="">ALL</a>
                                </li>
                                <li class="nav-item monthly_commission_tabs" data-tab="Pending_monthly_commission_tab"><a class="nav-link" data-toggle="tab" href="">Pending</a>
                                </li>
                                <li class="nav-item monthly_commission_tabs" data-tab="Success_monthly_commission_tab"><a class="nav-link" data-toggle="tab" href="">Success</a>
                                </li>
                                <li class="nav-item monthly_commission_tabs" data-tab="OnHold_monthly_commission_tab"><a class="nav-link" data-toggle="tab" href="">On Hold</a>
                                </li>
                                <li class="nav-item monthly_commission_tabs" data-tab="Cancelled_monthly_commission_tab"><a class="nav-link" data-toggle="tab" href="">Cancelled</a>
                                </li>
                                <li class="nav-item monthly_commission_tabs" data-tab="Failed_monthly_commission_tab"><a class="nav-link" data-toggle="tab" href="">Failed</a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-pane fade show active table-responsive" id="ALL_monthly_commission_tab">
                            <table id="Monthly_Commission" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>User</th>
                                    <th>Total Amount</th>
                                    <th>Commission Status</th>
                                    <th>Payment Date</th>
                                    <th>Month</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No.</th>
                                    <th>User</th>
                                    <th>Total Amount</th>
                                    <th>Commission Status</th>
                                    <th>Payment Date</th>
                                    <th>Month</th>
                                    <th>Action</th>
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

    <div class="modal fade" id="PayCommissionModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pay Commission</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to Pay this Commission?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="PayCommissionSubmit" type="submit">Pay <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- Commission  JS start -->
<script type="text/javascript">
function get_monthly_commission_page_tabType(){
    var tab_type;
    $('.monthly_commission_tabs').each(function() {
        var thi = $(this);
        if($(thi).find('a').hasClass('show')){
            tab_type = $(thi).attr('data-tab');
        }
    });
    return tab_type;
}

$(document).ready(function() {
    monthly_commission_table('',true);
});

function monthly_commission_table(tab_type='',is_clearState=false){
    if(is_clearState){
        $('#Monthly_Commission').DataTable().state.clear();
    }

    $('#Monthly_Commission').DataTable({
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
            "url": "{{ url('admin/allMonthlyCommissionlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}', tab_type: tab_type},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "20px", "targets": 0 },
            { "width": "120px", "targets": 1 },
            { "width": "100px", "targets": 2 },
            { "width": "150px", "targets": 3 },
            { "width": "150px", "targets": 4 },
            { "width": "120px", "targets": 5 },
            { "width": "100px", "targets": 6 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false ,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'user_info', name: 'user_info', orderable: false, class: "text-center multirow"},
            {data: 'total_amount', name: 'total_amount', class: "text-left"},
            {data: 'commission_status', name: 'commission_status', orderable: false, class: "text-center"},
            {data: 'payment_date', name: 'payment_date', class: "text-left"},
            {data: 'month', name: 'month', orderable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

$('body').on('click', '.monthly_commission_tabs', function () {
    var tab_type = $(this).attr('data-tab');
    monthly_commission_table(tab_type,true);
});

$('body').on('click', '.ViewMonthlyCommissionBtn', function () {
    var id = $(this).attr('data-id');
    var url = "{{ url('admin/viewMonthlyCommission') }}" + "/" + id;
    window.open(url,"_blank");
});

$('body').on('click', '#PayCommissionBtn', function (e) {
    // e.preventDefault();
    var Commission_id = $(this).attr('data-id');
    $("#PayCommissionModal").find('#PayCommissionSubmit').attr('data-id',Commission_id);
});

$('#PayCommissionModal').on('hidden.bs.modal', function () {
    $(this).find("#PayCommissionSubmit").removeAttr('data-id');
});

$('body').on('click', '#PayCommissionSubmit', function (e) {
    $('#PayCommissionSubmit').prop('disabled',true);
    $(this).find('.removeloadericonfa').show();
    e.preventDefault();
    var tab_type = get_monthly_commission_page_tabType();

    var Commission_id = $(this).attr('data-id');
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/monthly_commissions') }}" +'/' + Commission_id +'/pay',
        success: function (res) {
            if(res['status'] == 200){
                $("#PayCommissionModal").modal('hide');
                $('#PayCommissionSubmit').prop('disabled',false);
                $("#PayCommissionSubmit").find('.removeloadericonfa').hide();
                monthly_commission_table(tab_type,false);
                toastr.success("Commission Paid",'Success',{timeOut: 5000});
            }

            if(res['status'] == 400){
                $("#PayCommissionModal").modal('hide');
                $('#PayCommissionSubmit').prop('disabled',false);
                $("#PayCommissionSubmit").find('.removeloadericonfa').hide();
                monthly_commission_table(tab_type,false);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#PayCommissionModal").modal('hide');
            $('#PayCommissionSubmit').prop('disabled',false);
            $("#PayCommissionSubmit").find('.removeloadericonfa').hide();
            monthly_commission_table(tab_type,false);
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});
</script>
<!-- Commission  JS end -->
@endsection


