<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>
<script src="{{ url('public/js/AdBannerImg.js') }}" type="text/javascript"></script>

<script type="text/javascript">
var table;

$('body').on('click', '#AddAdgroupBtn', function () {
    location.href = "{{ route('admin.adgroups.add') }}";
});

$(document).ready(function() {
    adgroup_page_tabs('',true);

    $('#category_id').select2({
        width: '100%',
        placeholder: "Select...",
        allowClear: false
    });

    $('#ad_view_id').select2({
        width: '100%',
        placeholder: "Select...",
        allowClear: false
    });

    //For edit
    $(".category_dropdown").each(function() {
       var id = $(this).attr("id");
        $("#"+id).select2({
            width: '100%',
            placeholder: "Select Category",
            allowClear: false
        });
    });

    //For edit
    $(".product_dropdown").each(function() {
        var id = $(this).attr("id");
        $("#"+id).select2({
            width: '100%',
            placeholder: "Select Product",
            allowClear: true
        });
    });

    $('#adgroups tbody').on('click', 'td.details-control', function () {
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

function get_adgroups_page_tabType(){
    var tab_type;
    $('.adgroup_page_tabs').each(function() {
        var thi = $(this);
        if($(thi).find('a').hasClass('show')){
            tab_type = $(thi).attr('data-tab');
        }
    });
    return tab_type;
}

$(".adgroup_page_tabs").click(function() {
    var tab_type = $(this).attr('data-tab');
    adgroup_page_tabs(tab_type,true);
});

$('#ad_view_id').change(function() {
    var ad_view_id = $(this).val();
    $('#attr-cover-spin').show();
    $.ajax ({
        type:"POST",
        url: "{{ route('admin.adgroups.addBannerForm') }}",
        data : {ad_view_id: ad_view_id, "_token": "{{csrf_token()}}"},
        success: function(data) {
            if(ad_view_id == 1){
                $("#banner_view").html(banner_view());
            }
            else{
                $("#banner_view").html("");
            }
            $('#groupInfoBox').html(data.html);
        },
        complete: function(){
            $('#attr-cover-spin').hide();
        }
    });
});

function banner_view(){
    var html = `<div class="form-group ">
                    <label class="col-form-label" for="banner_view">Banner View
                    </label>
                    <div>
                        <label class="radio-inline mr-3"><input type="radio" name="banner_view" value="1" checked> Horizontal</label>
                        <label class="radio-inline mr-3"><input type="radio" name="banner_view" value="2"> Vertical</label>
                    </div>
                </div>`;

    return html;
}

$('#AddMoreBannerBtn').click(function() {
    $(this).prop('disabled',true);

    var ad_view_id = $("#ad_view_id").val();
    $('#attr-cover-spin').show();
    var last_form = $(".adGroupDataBox").length;
    $.ajax ({
        type:"POST",
        url: "{{ route('admin.adgroups.addBannerForm') }}",
        data : {ad_view_id: ad_view_id, "_token": "{{csrf_token()}}", last_form: last_form},
        success: function(data) {
            $('#groupInfoBox').append(data.html);
        },
        complete: function(){
            $('#attr-cover-spin').hide();
            $("#AddMoreBannerBtn").prop('disabled',false);
        }
    });
});

$('#SubmitAdGroupBtn').click(function() {
    $(this).prop('disabled',true);
    $(this).find('.submitloader').show();
    var btn = $(this);

    $("*#AdBannerImg-error").html("");
    $("*#AdBannerImg-error").hide();
    $("*#value-error").html("");
    $("*#value-error").hide();

    var valid_adgroup = validateAdGroupForm();
    var valid_adbanners = validateAdBannerForms();

    if(valid_adgroup==true && valid_adbanners==true) {
        var formData = new FormData($('#AdGroupForm')[0]);
        formData.append("total_AdBannerForm", $('.AdBannerForm').length);
        var cnt = 1;
        $('.AdBannerForm').each(function () {
            var thi = $(this);
            var AdBannerForm = $(this).serialize();
            formData.append("AdBannerForm" + cnt, AdBannerForm);
            cnt++;
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: "{{ route('admin.adgroups.save') }}",
            data: formData,
            dataType: 'json',
            cache: false,
            processData: false,
            contentType: false,
            success: function (res) {
                if(res['status']==200){
                    location.href = "{{ route('admin.adgroups.list') }}";
                    toastr.success("Ad Group Added",'Success',{timeOut: 5000});
                }
            },
            error: function (data) {
                $(btn).prop('disabled',false);
                $(btn).find('.submitloader').hide();
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        });
    }
    else{
        $(btn).prop('disabled',false);
        $(btn).find('.submitloader').hide();
    }
});

function validateAdGroupForm() {
    $("#AdGroupForm").validate({
        rules: {
            category_id : {
                required: true,
            },
            group_title: {
                required: true,
            },
            group_bg_color: {
                required: true,
            },
        },

        messages : {
            category_id: {
                required: "Please select a Page"
            },
            group_title: {
                required: "Please provide a Group Title",
            },
            group_bg_color: {
                required: "Please provide a Group Background Color",
            },
        }
    });

    var valid = true;
    if (!$("#AdGroupForm").valid()) {
        valid = false;
    }

    return valid;
}

function validateAdBannerForms() {
    $(".AdBannerForm").each(function() {
        $(this).validate({
            rules: {
                ad_title : {
                    required: true,
                },
            },

            messages : {
                ad_title: {
                    required: "Please provide a Ad Title"
                },
            }
        });
    });

    var valid = true;
    $('.AdBannerForm').each(function () {
        var this_form = $(this);
        if (!$(this).valid()) {
            valid = false;
        }

        if($(this).find('input[name="AdBannerImg"]').val()==""){
            $(this).find("#AdBannerImg-error").html("Please provide Banner Image");
            $(this).find("#AdBannerImg-error").show();
            valid = false;
        }

        if($(this).find('input[name="value"]').val()==""){
            $(this).find("#value-error").html("Please provide a Value");
            $(this).find("#value-error").show();
            valid = false;
        }

        if($(this).find('select[name="value"]').val()==""){
            $(this).find("#value-error").html("Please provide a Value");
            $(this).find("#value-error").show();
            valid = false;
        }
    });

    return valid;
}

$('body').on("change",".AdBannerInfo",function() {
    var thi = $(this);
    var AdBannerInfo = $(this).val();
    var data_id = $(this).attr('data-id');

    if(AdBannerInfo == 3 || AdBannerInfo == 5 || AdBannerInfo == 7 || AdBannerInfo == 10 || AdBannerInfo == 14){
        $('#attr-cover-spin').show();
        $.ajax ({
            type:"POST",
            url: "{{ route('admin.adgroups.getBannerInfoVal') }}",
            data : {AdBannerInfo: AdBannerInfo, "_token": "{{csrf_token()}}"},
            success: function(data) {
                // console.log(data);
                $(thi).parent().siblings('.AdBannerInfoBox').html(data.html);
                var cat_dropdown = $(thi).parent().siblings('.AdBannerInfoBox').find("#value");
                if(AdBannerInfo == 5 || AdBannerInfo == 7){
                    if(AdBannerInfo == 5){
                        category_dropdown(data.categories_catalog,cat_dropdown);
                    }
                    else{
                        category_dropdown(data.categories,cat_dropdown);
                    }
                    $(thi).parent().siblings('.AdBannerInfoBox').find("select[name='value']").attr('id',"value_"+data_id);
                    $(thi).parent().siblings('.AdBannerInfoBox').find("select[name='value']").attr('data-id',data_id);
                    $("#value_"+data_id).select2({
                        width: '100%',
                        placeholder: "Select Category",
                        allowClear: false
                    });
                }
                $(thi).parent().siblings('.productDropdownBox').html("");
            },
            complete: function(){
                $('#attr-cover-spin').hide();
            }
        });
    } else {
        $(thi).parent().siblings('.AdBannerInfoBox').html("");
        $(thi).parent().siblings('.productDropdownBox').html("");
    }
});

function category_dropdown(categories,cat_dropdown) {
    $.each(categories, function(index, item) {
        cat_dropdown.append(new Option(item.category_name, item.id));
    });
}

$('body').on("change",".category_dropdown_catalog",function(){
    $("#attr-cover-spin").fadeIn();
    var category_id = $(this).val();
    var productDropdownBox = $(this).parents('.AdBannerInfoBox:first').siblings(".productDropdownBox");
    var data_id = $(this).attr('data-id');

    $.get("{{ url('admin/adgroups/getproducts') }}" + '/' + category_id, function (data) {
        if (data) {
            var html =`<div class="form-group" id="">
                    <label class="col-form-label" for="product">Select Product</label>
                    <select id="product" name="product" class="">
                        <option></option>
                    </select>
                    <div id="product-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                    </div>`;

            $(productDropdownBox).html(html);
            $.each(data, function(index, item) {
                $(productDropdownBox).find("#product").append(new Option(item.product_title, item.id));
            });
            $(productDropdownBox).find("select[name='product']").attr('id',"product_"+data_id);
            $("#product_"+data_id).select2({
                width: '100%',
                placeholder: "Select Product",
                allowClear: true
            });
            $("#attr-cover-spin").fadeOut();
        } else {
            $(productDropdownBox).html("");
            $("#attr-cover-spin").fadeOut();
        }
    });
});

function adgroup_page_tabs(tab_type='',is_clearState=false) {
    if(is_clearState){
        $('#adgroups').DataTable().state.clear();
    }

    table = $('#adgroups').DataTable({
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
            "url": "{{ url('admin/alladgroupslist') }}",
            "dataType": "json",
            "type": "POST",
            "data":{ _token: '{{ csrf_token() }}' ,tab_type: tab_type},
            // "dataSrc": ""
        },
        'columnDefs': [
            { "width": "20px", "targets": 0 },
            { "width": "50px", "targets": 1 },
            { "width": "165px", "targets": 2 },
            { "width": "230px", "targets": 3 },
            { "width": "75px", "targets": 4 },
            { "width": "150px", "targets": 5 },
            { "width": "115px", "targets": 6 },
        ],
        "columns": [
            {"className": 'details-control', "orderable": false, "data": null, "defaultContent": ''},
            {data: 'id', name: 'id', class: "text-center", orderable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {data: 'category_id', name: 'category_id', class: "text-left"},
            // {data: 'group_title', name: 'group_title', class: "text-left"},
            {data: 'ad_view_id', name: 'ad_view_id', class: "text-left", orderable: false},
            {data: 'estatus', name: 'estatus', orderable: false, searchable: false, class: "text-center"},
            {data: 'created_at', name: 'created_at', searchable: false, class: "text-left"},
            {data: 'action', name: 'action', orderable: false, searchable: false, class: "text-center"},
        ]
    });
}

function format ( d ) {
    // `d` is the original data object for the row
    return d.table1;
}

function changeAdGroupStatus(adgroup_id) {
    var tab_type = get_adgroups_page_tabType();

    $.ajax({
        type: 'GET',
        url: "{{ url('admin/changeAdGroupstatus') }}" +'/' + adgroup_id,
        success: function (res) {
            if(res.status == 200 && res.action=='deactive'){
                $("#AdGroupstatuscheck_"+adgroup_id).val(2);
                $("#AdGroupstatuscheck_"+adgroup_id).prop('checked',false);
                adgroup_page_tabs(tab_type);
                toastr.success("Ad Group Deactivated",'Success',{timeOut: 5000});
            }
            if(res.status == 200 && res.action=='active'){
                $("#AdGroupstatuscheck_"+adgroup_id).val(1);
                $("#AdGroupstatuscheck_"+adgroup_id).prop('checked',true);
                adgroup_page_tabs(tab_type);
                toastr.success("Ad Group activated",'Success',{timeOut: 5000});
            }
        },
        error: function (data) {
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
}

$('#DeleteAdGroupModal').on('hidden.bs.modal', function () {
    $(this).find("#RemoveAdGroupSubmit").removeAttr('data-id');
});

$('body').on('click', '#deleteAdGroupBtn', function (e) {
    // e.preventDefault();
    var delete_adgroup_id = $(this).attr('data-id');
    $("#DeleteAdGroupModal").find('#RemoveAdGroupSubmit').attr('data-id',delete_adgroup_id);
});

$('body').on('click', '#RemoveAdGroupSubmit', function (e) {
    $('#RemoveAdGroupSubmit').prop('disabled',true);
    $(this).find('.removeloadericonfa').show();
    e.preventDefault();
    var remove_adgroup_id = $(this).attr('data-id');

    var tab_type = get_adgroups_page_tabType();

    $.ajax({
        type: 'GET',
        url: "{{ url('admin/adgroups') }}" +'/' + remove_adgroup_id +'/delete',
        success: function (res) {
            if(res.status == 200){
                $("#DeleteAdGroupModal").modal('hide');
                $('#RemoveAdGroupSubmit').prop('disabled',false);
                $("#RemoveAdGroupSubmit").find('.removeloadericonfa').hide();
                adgroup_page_tabs(tab_type);
                toastr.success("Ad Group Deleted",'Success',{timeOut: 5000});
            }

            if(res.status == 400){
                $("#DeleteAdGroupModal").modal('hide');
                $('#RemoveAdGroupSubmit').prop('disabled',false);
                $("#RemoveAdGroupSubmit").find('.removeloadericonfa').hide();
                adgroup_page_tabs(tab_type);
                toastr.error("Please try again",'Error',{timeOut: 5000});
            }
        },
        error: function (data) {
            $("#DeleteAdGroupModal").modal('hide');
            $('#RemoveAdGroupSubmit').prop('disabled',false);
            $("#RemoveAdGroupSubmit").find('.removeloadericonfa').hide();
            adgroup_page_tabs(tab_type);
            toastr.error("Please try again",'Error',{timeOut: 5000});
        }
    });
});

$('body').on('click', '#editAdGroupBtn', function () {
    var adgroup_id = $(this).attr('data-id');
    var url = "{{ url('admin/adgroups') }}" + "/" + adgroup_id + "/edit";
    window.open(url,"_blank");
});

function removeuploadedimg(divId ,inputId, fileInput){
    if(confirm("Are you sure you want to remove this file?")){
        $("#"+divId).remove();
        $("#"+inputId).removeAttr('value');
        var filerKit = $("#"+fileInput).prop("jFiler");
        filerKit.reset();
    }
}
</script>
