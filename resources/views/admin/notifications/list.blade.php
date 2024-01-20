@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Application</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Notifications</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            @if(isset($action) && $action=='create')
                                Add Notification
                            @elseif(isset($action) && $action=='edit')
                                Edit Notification
                            @else
                                Notifications List
                            @endif
                        </h4>

                        <div class="action-section">
                            <div class="d-flex">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.notifications.list')->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                    <button type="button" class="btn btn-primary" id="AddNotificationBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                {{-- <button class="btn btn-danger" onclick="deleteMultipleAttributes()"><i class="fa fa-trash" aria-hidden="true"></i></button>--}}
                            </div>
                        </div>

                        @if(isset($action) && $action=='list')
                            <div class="table-responsive">
                                <table id="Notification" class="table zero-configuration customNewtable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Image</th>
                                        <th>Notification Title</th>
                                        <th>Notification Desc</th>
                                        <th>Notification Type</th>
                                        <th>Notification Value</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>No.</th>
                                        <th>Image</th>
                                        <th>Notification Title</th>
                                        <th>Notification Desc</th>
                                        <th>Notification Type</th>
                                        <th>Notification Value</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                        @if(isset($action) && $action=='create')
                            @include('admin.notifications.create')
                        @endif

                        @if(isset($action) && $action=='edit')
                            @include('admin.notifications.edit')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteNotificationModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Notification</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Notification?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveNotificationSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
<script src="{{ url('public/js/NotificationImgJs.js') }}" type="text/javascript"></script>
<!-- Notification JS start -->
<script type="text/javascript">
$('body').on('click', '#AddNotificationBtn', function () {
    location.href = "{{ route('admin.notifications.add') }}";
});

$('#NotificationInfo').change(function() {
    var NotificationInfo = $(this).val();
    if(NotificationInfo == 3 || NotificationInfo == 5 || NotificationInfo == 7 || NotificationInfo == 10 || NotificationInfo == 14){
        $('#attr-cover-spin').show();
        $.ajax ({
            type:"POST",
            url: "{{ route('admin.notifications.getNotificationInfoVal') }}",
            data : {NotificationInfo: NotificationInfo, "_token": "{{csrf_token()}}"},
            success: function(data) {
                // console.log(data.categories);
                $('#infoBox').html(data.html);
                $("#productDropdownBox").html("");
                if(NotificationInfo == 5 || NotificationInfo == 7){
                    category_dropdown(data.categories);
                    $('#value').select2({
                        width: '100%',
                        placeholder: "Select Category",
                        allowClear: false
                    });
                }
            },
            complete: function(){
                $('#attr-cover-spin').hide();
            }
        });
    } else {
        $('#infoBox').html('');
        $("#productDropdownBox").html("");
    }
});

function category_dropdown(categories) {
    var list = $("#value");
    $.each(categories, function(index, item) {
        list.append(new Option(item.category_name, item.id));
    });
}

$(document).ready(function() {
    notification_table(true);
    $('#value').select2({
        width: '100%',
        placeholder: "Select Category",
        allowClear: false
    });
    $('#product').select2({
        width: '100%',
        placeholder: "Select Product",
        allowClear: true
    });
});

$('body').on("change",".category_dropdown_catalog",function(){
    $("#attr-cover-spin").fadeIn();
    var category_id = $(this).val();

    $.get("{{ url('admin/notifications/getproducts') }}" + '/' + category_id, function (data) {
        if (data) {
            var html =`<div class="form-group" id="">
            <label class="col-form-label" for="product">Select Product</label>
            <select id="product" name="product" class="">
                <option></option>
            </select>
            <div id="product-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
            </div>`;

            $("#productDropdownBox").html(html);
            $.each(data, function(index, item) {
                $("#product").append(new Option(item.product_title, item.id));
            });
            $('#product').select2({
                width: '100%',
                placeholder: "Select Product",
                allowClear: true
            });
            $("#attr-cover-spin").fadeOut();
        } else {
            $("#productDropdownBox").html("");
            $("#attr-cover-spin").fadeOut();
        }
    });
});

$('body').on('click', '#save_closeNotificationBtn', function () {
    save_Notification($(this),'save_close');
});

$('body').on('click', '#save_newNotificationBtn', function () {
    save_Notification($(this),'save_new');
});

function save_Notification(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();
    var action  = $(btn).attr('data-action');

    var formData = new FormData($("#NotificationForm")[0]);
    formData.append('action',action);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.notifications.save') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if(res.status == 'failed'){
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();

                if (res.errors.notify_title) {
                    $('#notify_title-error').show().text(res.errors.notify_title);
                } else {
                    $('#notify_title-error').hide();
                }

                if (res.errors.notify_desc) {
                    $('#notify_desc-error').show().text(res.errors.notify_desc);
                } else {
                    $('#notify_desc-error').hide();
                }

                if (res.errors.NotificationImg) {
                    $('#NotificationImg-error').show().text(res.errors.NotificationImg);
                } else {
                    $('#NotificationImg-error').hide();
                }

                if (res.errors.value) {
                    if($("#NotificationInfo").val() == 3) {
                        $('#value-error').show().text("Please provide a Price");
                    }
                    else if($("#NotificationInfo").val() == 5) {
                        $('#value-error').show().text("Please provide a Category");
                    }
                    else if($("#NotificationInfo").val() == 7) {
                        $('#value-error').show().text("Please provide a Category");
                    }
                    else if($("#NotificationInfo").val() == 10) {
                        $('#value-error').show().text("Please provide a Arrival Days");
                    }
                    else if($("#NotificationInfo").val() == 14) {
                        $('#value-error').show().text("Please provide a Banner URL");
                    }
                } else {
                    $('#value-error').hide();
                }
            }

            if(res.status == 200){
                if(btn_type == 'save_close'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.notifications.list')}}";
                    if(res.action == 'add'){
                        toastr.success("Notification Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Notification Updated",'Success',{timeOut: 5000});
                    }
                }
                if(btn_type == 'save_new'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.notifications.add')}}";
                    if(res.action == 'add'){
                        toastr.success("Notification Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Notification Updated",'Success',{timeOut: 5000});
                    }
                }
            }

        },
        error: function (data) {
            $(btn).prop('disabled',false);
            $(btn).find('.loadericonfa').hide();
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
}

function notification_table(is_clearState=false){
    if(is_clearState){
        $('#Notification').DataTable().state.clear();
    }

    $('#Notification').DataTable({
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
            "url": "{{ url('admin/allnotificationlist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}'},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "50px", "targets": 0 },
            { "width": "120px", "targets": 1 },
            { "width": "170px", "targets": 2 },
            { "width": "240px", "targets": 3 },
            { "width": "150px", "targets": 4 },
            { "width": "100px", "targets": 5 },
            { "width": "100px", "targets": 6 },
            { "width": "100px", "targets": 7 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'notify_thumb', name: 'notify_thumb', orderable: false, searchable: false, class: "text-center"},
            {data: 'notify_title', name: 'notify_title', class: "text-left"},
            {data: 'notify_desc', name: 'notify_desc', class: "text-left"},
            {data: 'application_dropdown_id', name: 'application_dropdown_id', class: "text-left"},
            {data: 'value', name: 'value', class: "text-left"},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

$('body').on('click', '#editNotificationBtn', function () {
    var Notification_id = $(this).attr('data-id');
    var url = "{{ url('admin/notifications') }}" + "/" + Notification_id + "/edit";
    window.open(url,"_blank");
});

function removeuploadedimg(divId ,inputId, imgName){
    if(confirm("Are you sure you want to remove this file?")){
        $("#"+divId).remove();
        $("#"+inputId).removeAttr('value');
        var filerKit = $("#NotificationFiles").prop("jFiler");
        filerKit.reset();
    }
}

$('body').on('click', '#deleteNotificationBtn', function (e) {
    // e.preventDefault();
    var Notification_id = $(this).attr('data-id');
    $("#DeleteNotificationModal").find('#RemoveNotificationSubmit').attr('data-id',Notification_id);
});

$('body').on('click', '#RemoveNotificationSubmit', function (e) {
    $('#RemoveNotificationSubmit').prop('disabled',true);
    $(this).find('.removeloadericonfa').show();
    e.preventDefault();
    var Notification_id = $(this).attr('data-id');
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/notifications') }}" +'/' + Notification_id +'/delete',
        success: function (res) {
            if(res.status == 200){
                $("#DeleteNotificationModal").modal('hide');
                $('#RemoveNotificationSubmit').prop('disabled',false);
                $("#RemoveNotificationSubmit").find('.removeloadericonfa').hide();
                notification_table();
                toastr.success("Notification Deleted",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $("#DeleteNotificationModal").modal('hide');
                $('#RemoveNotificationSubmit').prop('disabled',false);
                $("#RemoveNotificationSubmit").find('.removeloadericonfa').hide();
                notification_table();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#DeleteNotificationModal").modal('hide');
            $('#RemoveNotificationSubmit').prop('disabled',false);
            $("#RemoveNotificationSubmit").find('.removeloadericonfa').hide();
            notification_table();
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$('#DeleteNotificationModal').on('hidden.bs.modal', function () {
    $(this).find("#RemoveNotificationSubmit").removeAttr('data-id');
});

$('body').on('click', '#sendNotificationBtn', function (e) {
    $('#sendNotificationBtn').prop('disabled',true);
    e.preventDefault();
    var Notification_id = $(this).attr('data-id');
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/notifications') }}" +'/' + Notification_id +'/send',
        success: function (res) {
            if(res.status == 200){
                $('#sendNotificationBtn').prop('disabled',false);
                toastr.success("Notification Sent",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $('#sendNotificationBtn').prop('disabled',false);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $('#sendNotificationBtn').prop('disabled',false);
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

</script>
<!-- Notification JS end -->
@endsection
