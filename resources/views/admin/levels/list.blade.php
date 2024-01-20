@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Application</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Levels</a></li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Levels List</h4>

                        <div class="action-section">
                            <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.levels.list')->pluck('id')->first(); ?>
                            @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#LevelModal" id="AddLevelBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            @endif
                            {{-- <button class="btn btn-danger" onclick="deleteMultipleAttributes()"><i class="fa fa-trash" aria-hidden="true"></i></button>--}}
                        </div>
                        <div class="table-responsive">
                            <table id="Level" class="table zero-configuration customNewtable" style="width:100%">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Commission (%)</th>
                                    <th>Child Users</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tfoot>
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Commission (%)</th>
                                    <th>Child Users</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="LevelModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="form-valide" action="" id="LevelForm" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="formtitle">Add New Level</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="attr-cover-spin" class="cover-spin"></div>
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-form-label" for="title">Title
                            </label>
                            <input type="text" class="form-control input-flat" id="title" name="title" placeholder="" readonly>
                            <div id="title-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="commission_percentage">Commission (%) <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control input-flat" id="commission_percentage" name="commission_percentage" placeholder="">
                            <div id="commission_percentage-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                        <div class="form-group">
                            <label class="col-form-label" for="no_child_users">Child Users <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control input-flat" id="no_child_users" name="no_child_users" placeholder="">
                            <div id="no_child_users-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="level_id" id="level_id">
                        {{--                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                        <button type="button" class="btn btn-outline-primary" id="save_newLevelBtn">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                        <button type="button" class="btn btn-primary" id="save_closeLevelBtn">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('js')
<!-- Levels JS start -->
<script type="text/javascript">
var level = "{{ getLastLevel() + 1 }}";

$(document).ready(function() {
    level_table(true);
});

$('body').on('click', '#AddLevelBtn', function (e) {
    $("#LevelModal").find('.modal-title').html("Add New Level");
    $("#LevelModal").find('#title').val("Level "+level);
});

$('#LevelModal').on('shown.bs.modal', function (e) {
    $("#commission_percentage").focus();
});

$('#LevelModal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset');
    $(this).find("#save_newLevelBtn").removeAttr('data-action');
    $(this).find("#save_closeLevelBtn").removeAttr('data-action');
    $(this).find("#save_newLevelBtn").removeAttr('data-id');
    $(this).find("#save_closeLevelBtn").removeAttr('data-id');
    $('#level_id').val("");
    $('#commission_percentage-error').html("");
    $('#no_child_users-error').html("");
});

$('body').on('click', '#save_newLevelBtn', function () {
    save_level($(this),'save_new');
});

$('body').on('click', '#save_closeLevelBtn', function () {
    save_level($(this),'save_close');
});

function save_level(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();
    var action  = $(btn).attr('data-action');

    var formData = new FormData($("#LevelForm")[0]);
    formData.append('action',action);

    $.ajax({
        type: 'POST',
        url: "{{ url('admin/addorupdateLevel') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if(res.status == 'failed'){
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                if (res.errors.commission_percentage) {
                    $('#commission_percentage-error').show().text(res.errors.commission_percentage);
                } else {
                    $('#commission_percentage-error').hide();
                }

                if (res.errors.no_child_users) {
                    $('#no_child_users-error').show().text(res.errors.no_child_users);
                } else {
                    $('#no_child_users-error').hide();
                }
            }

            if(res.status == 200){
                level = res.level;
                if(btn_type == 'save_close'){
                    $("#LevelModal").modal('hide');
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    if(res.action == 'add'){
                        level_table(true);
                        toastr.success("Level Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        level_table();
                        toastr.success("Level Updated",'Success',{timeOut: 5000});
                    }
                }

                if(btn_type == 'save_new'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    $("#LevelModal").find('form').trigger('reset');
                    $("#LevelModal").find("#save_newLevelBtn").removeAttr('data-action');
                    $("#LevelModal").find("#save_closeLevelBtn").removeAttr('data-action');
                    $("#LevelModal").find("#save_newLevelBtn").removeAttr('data-id');
                    $("#LevelModal").find("#save_closeLevelBtn").removeAttr('data-id');
                    $('#level_id').val("");
                    $('#commission_percentage-error').html("");
                    $('#no_child_users-error').html("");
                    $("#commission_percentage").focus();
                    $("#LevelModal").find('#title').val("Level "+level);
                    if(res.action == 'add'){
                        level_table(true);
                        toastr.success("Level Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        level_table();
                        toastr.success("Level Updated",'Success',{timeOut: 5000});
                    }
                }
            }

            if(res.status == 400){
                $("#LevelModal").modal('hide');
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();
                level_table();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#LevelModal").modal('hide');
            $(btn).prop('disabled',false);
            $(btn).find('.loadericonfa').hide();
            level_table();
            toastr.error("Please try again",'Error',{timeOut: 5000});
        },
        complete: function() {

        }
    });
}

function level_table(is_clearState=false){
    if(is_clearState){
        $('#Level').DataTable().state.clear();
    }

    $('#Level').DataTable({
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
            "url": "{{ url('admin/allLevelList') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}' },
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "50px", "targets": 0 },
            { "width": "150px", "targets": 1 },
            { "width": "150px", "targets": 2 },
            { "width": "150px", "targets": 3 },
            { "width": "120px", "targets": 4 },
            { "width": "120px", "targets": 5 },
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false ,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'title', name: 'title', class: "text-left"},
            {data: 'commission_percentage', name: 'commission_percentage', class: "text-center"},
            {data: 'no_child_users', name: 'no_child_users', class: "text-center"},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

$('body').on('click', '#editLevelBtn', function () {
    var level_id = $(this).attr('data-id');
    $.get("{{ url('admin/levels') }}" +'/' + level_id +'/edit', function (data) {
        $('#LevelModal').find('.modal-title').html("Edit Level");
        $('#LevelModal').find('#save_newLevelBtn').attr("data-action","update");
        $('#LevelModal').find('#save_closeLevelBtn').attr("data-action","update");
        $('#LevelModal').find('#save_newLevelBtn').attr("data-id",level_id);
        $('#LevelModal').find('#save_closeLevelBtn').attr("data-id",level_id);
        $('#level_id').val(data.id);
        $('#title').val(data.title);
        $('#commission_percentage').val(data.commission_percentage);
        $('#no_child_users').val(data.no_child_users);
    })
});

</script>
<!-- Levels JS end -->
@endsection
