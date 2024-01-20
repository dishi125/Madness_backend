@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Order Report</a></li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Order Report
                        </h4>

                        <div class="action-section">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control" id="user_id_filter">
                                    <option></option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 input-group">
                                    <input type="text" class="form-control custom_date_picker" id="start_date" name="start_date" placeholder="Start Date" data-date-format="yyyy-mm-dd" data-date-end-date="0d"> <span class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar-check"></i></span></span>
                                </div>
                                <div class="col-md-3 input-group">
                                    <input type="text" class="form-control custom_date_picker" id="end_date" name="end_date" placeholder="End Date" data-date-format="yyyy-mm-dd" data-date-end-date="0d"> <span class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar-check"></i></span></span>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-primary" id="export_excel_btn" >Export to Excel <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="order_report" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>No.</th>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Note</th>
                                    <th>Payment Status</th>
                                    <th>Order Status</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th></th>
                                    <th>No.</th>
                                    <th>Order</th>
                                    <th>Customer</th>
                                    <th>Note</th>
                                    <th>Payment Status</th>
                                    <th>Order Status</th>
                                    <th>Date</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- order report JS start -->
<script type="text/javascript">
var table;

$(document).ready(function() {
    $('#user_id_filter').select2({
        width: '100%',
        placeholder: "Select User",
        allowClear: true
    });

    order_report_table(true);

    $('#order_report tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    });
});

function format ( d ) {
    // `d` is the original data object for the row
    return d.table1;
}

function order_report_table(is_clearState=false){
    if(is_clearState){
        $('#order_report').DataTable().state.clear();
    }
    var user_id_filter = $("#user_id_filter").val();
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();

    table = $('#order_report').DataTable({
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
        // dom: "Blfrtip",
        buttons: [
            {
                extend: 'excel',
                // text: 'Export to Excel',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            }
        ],
        "ajax":{
            "url": "{{ url('admin/allOrderReportlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}', user_id_filter: user_id_filter, start_date: start_date, end_date: end_date},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "20px", "targets": 0 },
            { "width": "50px", "targets": 1 },
            { "width": "230px", "targets": 2 },
            { "width": "230px", "targets": 3 },
            { "width": "150px", "targets": 4 },
            { "width": "120px", "targets": 5 },
            { "width": "200px", "targets": 6 },
            { "width": "120px", "targets": 7 },
        ],
        "columns": [
            {"className": 'details-control', "orderable": false, "data": null, "defaultContent": ''},
            {data: 'id', name: 'id', class: "text-center", orderable: false ,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'order_info', name: 'order_info', orderable: false, class: "text-left multirow"},
            {data: 'customer_info', name: 'customer_info', orderable: false, class: "text-left multirow"},
            {data: 'note', name: 'note', orderable: false, class: "text-center"},
            {data: 'payment_status', name: 'payment_status', orderable: false, class: "text-center multirow"},
            {data: 'order_status', name: 'order_status', orderable: false, class: "text-center"},
            {data: 'created_at', name: 'created_at', orderable: false, class: "text-left multirow"},
        ]
    });
}

$('body').on('change', '#user_id_filter', function (e) {
    // e.preventDefault();
    order_report_table(true);
});

$('body').on('change', '#start_date', function (e) {
    // e.preventDefault();
    order_report_table(true);
});

$('body').on('change', '#end_date', function (e) {
    // e.preventDefault();
    order_report_table(true);
});

$("#export_excel_btn").on("click", function() {
    table.button( '.buttons-excel' ).trigger();
});
</script>
<!-- order report JS end -->
@endsection

