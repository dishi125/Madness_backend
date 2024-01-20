@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Application</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Collections</a></li>
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
                                Add Collection
                            @elseif(isset($action) && $action=='edit')
                                Edit Collection
                            @else
                                Collections List
                            @endif
                        </h4>

                        <div class="action-section">
                            <div class="d-flex">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.collections.list')->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                    <button type="button" class="btn btn-primary" id="AddCollectionBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                {{-- <button class="btn btn-danger" onclick="deleteMultipleAttributes()"><i class="fa fa-trash" aria-hidden="true"></i></button>--}}
                            </div>
                        </div>

                        @if(isset($action) && $action=='list')
                            <div class="table-responsive">
                                <table id="Collection" class="table zero-configuration customNewtable" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Sr. No</th>
                                        <th>Image</th>
                                        <th>Collection Title</th>
                                        <th>Collection Type</th>
                                        <th>Collection Value</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th>Sr. No</th>
                                        <th>Image</th>
                                        <th>Collection Title</th>
                                        <th>Collection Type</th>
                                        <th>Collection Value</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @endif

                        @if(isset($action) && $action=='create')
                            @include('admin.collections.create')
                        @endif

                        @if(isset($action) && $action=='edit')
                            @include('admin.collections.edit')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="DeleteCollectionModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remove Collection</h5>
                </div>
                <div class="modal-body">
                    Are you sure you wish to remove this Collection?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-default" data-dismiss="modal" type="button">Cancel</button>
                    <button class="btn btn-danger" id="RemoveCollectionSubmit" type="submit">Remove <i class="fa fa-circle-o-notch fa-spin removeloadericonfa" style="display:none;"></i></button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script src="{{ url('public/js/CollectionImgJs.js') }}" type="text/javascript"></script>
<!-- Collection JS start -->
<script type="text/javascript">
$('body').on('click', '#AddCollectionBtn', function () {
    location.href = "{{ route('admin.collections.add') }}";
});

$('#CollectionInfo').change(function() {
    var CollectionInfo = $(this).val();
    if(CollectionInfo == 3 || CollectionInfo == 5 || CollectionInfo == 7 || CollectionInfo == 10 || CollectionInfo == 14){
        $('#attr-cover-spin').show();
        $.ajax ({
            type:"POST",
            url: "{{ route('admin.collections.getCollectionInfoVal') }}",
            data : {CollectionInfo: CollectionInfo, "_token": "{{csrf_token()}}"},
            success: function(data) {
                // console.log(data.categories);
                $('#infoBox').html(data.html);
                $("#productDropdownBox").html("");
                if(CollectionInfo == 5 || CollectionInfo == 7){
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

$('body').on('click', '#save_closeCollectionBtn', function () {
    save_collection($(this),'save_close');
});

$('body').on('click', '#save_newCollectionBtn', function () {
    save_collection($(this),'save_new');
});

function save_collection(btn,btn_type){
    $(btn).prop('disabled',true);
    $(btn).find('.loadericonfa').show();
    var action  = $(btn).attr('data-action');

    var formData = new FormData($("#CollectionForm")[0]);
    formData.append('action',action);

    $.ajax({
        type: 'POST',
        url: "{{ route('admin.collections.save') }}",
        data: formData,
        processData: false,
        contentType: false,
        success: function (res) {
            if(res.status == 'failed'){
                $(btn).prop('disabled',false);
                $(btn).find('.loadericonfa').hide();

                if (res.errors.sr_no) {
                    $('#srno-error').show().text(res.errors.sr_no);
                } else {
                    $('#srno-error').hide();
                }

                if (res.errors.title) {
                    $('#title-error').show().text(res.errors.title);
                } else {
                    $('#title-error').hide();
                }

                if (res.errors.CollectionImg) {
                    $('#CollectionImg-error').show().text(res.errors.CollectionImg);
                } else {
                    $('#CollectionImg-error').hide();
                }

                if (res.errors.value) {
                    if($("#CollectionInfo").val() == 3) {
                        $('#value-error').show().text("Please provide a Price");
                    }
                    else if($("#CollectionInfo").val() == 5) {
                        $('#value-error').show().text("Please provide a Category");
                    }
                    else if($("#CollectionInfo").val() == 7) {
                        $('#value-error').show().text("Please provide a Category");
                    }
                    else if($("#CollectionInfo").val() == 10) {
                        $('#value-error').show().text("Please provide a Arrival Days");
                    }
                    else if($("#CollectionInfo").val() == 14) {
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
                    location.href="{{ route('admin.collections.list')}}";
                    if(res.action == 'add'){
                        toastr.success("Collection Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Collection Updated",'Success',{timeOut: 5000});
                    }
                }
                if(btn_type == 'save_new'){
                    $(btn).prop('disabled',false);
                    $(btn).find('.loadericonfa').hide();
                    location.href="{{ route('admin.collections.add')}}";
                    if(res.action == 'add'){
                        toastr.success("Collection Added",'Success',{timeOut: 5000});
                    }
                    if(res.action == 'update'){
                        toastr.success("Collection Updated",'Success',{timeOut: 5000});
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

function collection_table(is_clearState=false){
    if(is_clearState){
        $('#Collection').DataTable().state.clear();
    }

    $('#Collection').DataTable({
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
            "url": "{{ url('admin/allcollectionlist') }}",
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
            {data: 'sr_no', name: 'sr_no', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'image', name: 'image', orderable: false, searchable: false, class: "text-center"},
            {data: 'title', name: 'title', class: "text-left"},
            {data: 'application_dropdown_id', name: 'application_dropdown_id', class: "text-left"},
            {data: 'value', name: 'value', class: "text-left"},
            {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

$(document).ready(function() {
    collection_table(true);
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

$('body').on('click', '#editCollectionBtn', function () {
    var Collection_id = $(this).attr('data-id');
    var url = "{{ url('admin/collections') }}" + "/" + Collection_id + "/edit";
    window.open(url,"_blank");
});

function removeuploadedimg(divId ,inputId, imgName){
    if(confirm("Are you sure you want to remove this file?")){
        $("#"+divId).remove();
        $("#"+inputId).removeAttr('value');
        var filerKit = $("#CollectionFiles").prop("jFiler");
        filerKit.reset();
    }
}

$('body').on('click', '#deleteCollectionBtn', function (e) {
    // e.preventDefault();
    var collection_id = $(this).attr('data-id');
    $("#DeleteCollectionModal").find('#RemoveCollectionSubmit').attr('data-id',collection_id);
});

$('body').on('click', '#RemoveCollectionSubmit', function (e) {
    $('#RemoveCollectionSubmit').prop('disabled',true);
    $(this).find('.removeloadericonfa').show();
    e.preventDefault();
    var collection_id = $(this).attr('data-id');
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/collections') }}" +'/' + collection_id +'/delete',
        success: function (res) {
            if(res.status == 200){
                $("#DeleteCollectionModal").modal('hide');
                $('#RemoveCollectionSubmit').prop('disabled',false);
                $("#RemoveCollectionSubmit").find('.removeloadericonfa').hide();
                collection_table();
                toastr.success("Collection Deleted",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $("#DeleteCollectionModal").modal('hide');
                $('#RemoveCollectionSubmit').prop('disabled',false);
                $("#RemoveCollectionSubmit").find('.removeloadericonfa').hide();
                collection_table();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#DeleteCollectionModal").modal('hide');
            $('#RemoveCollectionSubmit').prop('disabled',false);
            $("#RemoveCollectionSubmit").find('.removeloadericonfa').hide();
            collection_table();
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$('#DeleteCollectionModal').on('hidden.bs.modal', function () {
    $(this).find("#RemoveCollectionSubmit").removeAttr('data-id');
});

$('body').on("change",".category_dropdown_catalog",function(){
    $("#attr-cover-spin").fadeIn();
    var category_id = $(this).val();

    $.get("{{ url('admin/collections/getproducts') }}" + '/' + category_id, function (data) {
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

function changeCollectionStatus(Collection_id) {
    $.ajax({
        type: 'GET',
        url: "{{ url('admin/changeCollectionStatus') }}" +'/' + Collection_id,
        success: function (res) {
            if(res.status == 200 && res.action=='deactive'){
                $("#Collectionstatuscheck_"+Collection_id).val(2);
                $("#Collectionstatuscheck_"+Collection_id).prop('checked',false);
                toastr.success("Collection Deactivated",'Success',{timeOut: 5000});
            }
            if(res.status == 200 && res.action=='active'){
                $("#Collectionstatuscheck_"+Collection_id).val(1);
                $("#Collectionstatuscheck_"+Collection_id).prop('checked',true);
                toastr.success("Collection activated",'Success',{timeOut: 5000});
            }
        },
        error: function (data) {
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
}
</script>
<!-- Collection JS end -->
@endsection

