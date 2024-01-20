@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Commission Report</a></li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            Commission Report
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

                                <div class="col-md-6">
                                    <div class="input-daterange input-group filter_date" id="date-range" data-date-format='dd-mm-yyyy'>
                                        <input type="text" class="form-control" name="start_date" id="start_date"> <span class="input-group-addon bg-info b-0 text-white">to</span>
                                        <input type="text" class="form-control" name="end_date" id="end_date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-primary" id="export_excel_btn" >Export to Excel <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="commission_report" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>User</th>
                                    <th>Total Amount</th>
                                    <th>Commission Status</th>
                                    <th>Payment Date</th>
                                    <th>Month</th>
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
$(document).ready(function() {
    $('#user_id_filter').select2({
        width: '100%',
        placeholder: "Select User",
        allowClear: true
    });

    commission_report_table(true);
});

function commission_report_table(is_clearState=false){
    if(is_clearState){
        $('#commission_report').DataTable().state.clear();
    }
    var user_id_filter = $("#user_id_filter").val();
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();

    $('#commission_report').DataTable({
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
            "url": "{{ url('admin/allCommissionReportlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}', user_id_filter: user_id_filter, start_date: start_date, end_date: end_date},
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
            {data: 'user_info', name: 'user_info', orderable: false, class: "text-center multirow"},
            {data: 'total_amount', name: 'total_amount', class: "text-left"},
            {data: 'commission_status', name: 'commission_status', orderable: false, class: "text-center"},
            {data: 'payment_date', name: 'payment_date', class: "text-left"},
            {data: 'month', name: 'month', orderable: false, class: "text-left"},
        ]
    });
}

$('body').on('change', '#user_id_filter', function (e) {
    // e.preventDefault();
    commission_report_table(true);
});

$('body').on('change', '#start_date', function (e) {
    // e.preventDefault();
    commission_report_table(true);
});

$('body').on('change', '#end_date', function (e) {
    // e.preventDefault();
    commission_report_table(true);
});

$("#export_excel_btn").on("click", function() {
    $('#commission_report').DataTable().button( '.buttons-excel' ).trigger();
});
</script>
<!-- order report JS end -->
@endsection

