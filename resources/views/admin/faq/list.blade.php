@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">FAQ</a></li>
            </ol>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            @if(isset($action) && $action=='create')
                                Add FAQ
                            @elseif(isset($action) && $action=='edit')
                                Edit FAQ
                            @else
                                FAQ List
                            @endif
                        </h4>

                        <div class="action-section">
                            <div class="d-flex">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.faq.list')->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                    <button type="button" class="btn btn-primary" id="AddFaqBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                {{-- <button class="btn btn-danger" onclick="deleteMultipleAttributes()"><i class="fa fa-trash" aria-hidden="true"></i></button>--}}
                            </div>
                        </div>

                        @if(isset($action) && $action=='list')
                            <div class="table-responsive">
                                <table id="Faq" class="table zero-configuration customNewtable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Question</th>
                                        <th>Answer</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>No.</th>
                                        <th>Question</th>
                                        <th>Answer</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                        @if(isset($action) && $action=='create')
                            @include('admin.faq.create')
                        @endif

                        @if(isset($action) && $action=='edit')
                            @include('admin.faq.edit')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteFaqModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove FAQ</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this FAQ?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveFaqSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<!-- FAQ JS start -->
<script type="text/javascript">
$('body').on('click', '#AddFaqBtn', function () {
    location.href = "{{ route('admin.faq.add') }}";
});

$('body').on('click', '#save_closeFaqBtn', function () {
    save_faq($(this),'save_close');
});

$('body').on('click', '#save_newFaqBtn', function () {
    save_faq($(this),'save_new');
});

function save_faq(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();
    var action  = $(btn).attr('data-action');

    var formData = new FormData($("#FaqForm")[0]);
    formData.append('action',action);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.faq.save') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if(res.status == 'failed'){
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();

                if (res.errors.question) {
                    $('#question-error').show().text(res.errors.question);
                } else {
                    $('#question-error').hide();
                }

                if (res.errors.answer) {
                    $('#answer-error').show().text(res.errors.answer);
                } else {
                    $('#answer-error').hide();
                }
            }

            if(res.status == 200){
                if(btn_type == 'save_close'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.faq.list')}}";
                    if(res.action == 'add'){
                        toastr.success("FAQ Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("FAQ Updated",'Success',{timeOut: 5000});
                    }
                }
                if(btn_type == 'save_new'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.faq.add')}}";
                    if(res.action == 'add'){
                        toastr.success("FAQ Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("FAQ Updated",'Success',{timeOut: 5000});
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

function Faq_table(is_clearState=false){
    if(is_clearState){
        $('#Faq').DataTable().state.clear();
    }

    $('#Faq').DataTable({
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
            "url": "{{ url('admin/allFaqlist') }}",
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
        ],
        "columns": [
            {data: 'id', name: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'question', name: 'question'},
            {data: 'answer', name: 'answer'},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

$(document).ready(function() {
    Faq_table(true);
});

$('body').on('click', '#editFaqBtn', function () {
    var faq_id = $(this).attr('data-id');
    var url = "{{ url('admin/faq') }}" + "/" + faq_id + "/edit";
    window.open(url,"_blank");
});

$('body').on('click', '#deleteFaqBtn', function (e) {
    // e.preventDefault();
    var faq_id = $(this).attr('data-id');
    $("#DeleteFaqModal").find('#RemoveFaqSubmit').attr('data-id',faq_id);
});

$('body').on('click', '#RemoveFaqSubmit', function (e) {
    $('#RemoveFaqSubmit').prop('disabled',true);
    $(this).find('.removeloadericonfa').show();
    e.preventDefault();
    var faq_id = $(this).attr('data-id');
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/faq') }}" +'/' + faq_id +'/delete',
        success: function (res) {
            if(res.status == 200){
                $("#DeleteFaqModal").modal('hide');
                $('#RemoveFaqSubmit').prop('disabled',false);
                $("#RemoveFaqSubmit").find('.removeloadericonfa').hide();
                Faq_table();
                toastr.success("FAQ Deleted",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $("#DeleteFaqModal").modal('hide');
                $('#RemoveFaqSubmit').prop('disabled',false);
                $("#RemoveFaqSubmit").find('.removeloadericonfa').hide();
                Faq_table();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#DeleteFaqModal").modal('hide');
            $('#RemoveFaqSubmit').prop('disabled',false);
            $("#RemoveFaqSubmit").find('.removeloadericonfa').hide();
            Faq_table();
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$('#DeleteFaqModal').on('hidden.bs.modal', function () {
    $(this).find("#RemoveFaqSubmit").removeAttr('data-id');
});
</script>
<!-- FAQ JS end -->
@endsection


