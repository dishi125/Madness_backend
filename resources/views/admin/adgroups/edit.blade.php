@extends('admin.layout')

@section('content')
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0)">Application</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Ad Group</a></li>
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
                            Edit Ad Banners
                        </h4>

                        <div class="action-section">
                            <div class="d-flex">
                                <?php $page_id = \App\Models\ProjectPage::where('route_url','admin.adgroups.list')->pluck('id')->first(); ?>
                                @if(getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) )
                                    <button type="button" class="btn btn-primary" id="AddAdgroupBtn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                @endif
                                {{-- <button class="btn btn-danger" onclick="deleteMultipleAttributes()"><i class="fa fa-trash" aria-hidden="true"></i></button>--}}
                            </div>
                        </div>

                        <div id="attr-cover-spin" class="cover-spin"></div>
                        <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 container justify-content-center">
                            <form class="form-valide" action="" id="AdGroupForm" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" id="action" name="action" value="edit">
                                <input type="hidden" id="adgroup_id" name="adgroup_id" value="{{ $AdGroup->id }}">
                                <div class="form-group">
                                    <label class="col-form-label" for="Page">Select Page  <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="category_id" name="category_id">
                                        <option></option>
                                        <option value="0" @if($AdGroup->category_id == 0) selected @endif>Home Page</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id  }}" @if($category->id == $AdGroup->category_id) selected @endif>{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                    <label id="category_id-error" class="error invalid-feedback animated fadeInDown" for="category_id"></label>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label" for="GroupTitle">Group Title  <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control input-flat" id="group_title" name="group_title" placeholder="" value="{{ $AdGroup->group_title }}">
                                    <label id="group_title-error" class="error invalid-feedback animated fadeInDown" for="group_title"></label>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label" for="GroupBackgroundColor">Group Background Color  <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="colorpicker form-control input-flat" value="{{ $AdGroup->group_bg_color }}" id="group_bg_color" name="group_bg_color">
                                    <label id="group_bg_color-error" class="error invalid-feedback animated fadeInDown" for="group_bg_color"></label>
                                </div>

                                <div class="form-group ">
                                    <label class="col-form-label" for="display_adtitle_with_banner">Do you wish to display AD Title with Banner?
                                    </label>
                                    <div>
                                        <label class="radio-inline mr-3"><input type="radio" name="display_adtitle_with_banner" value="1" @if($AdGroup->display_adtitle_with_banner == 1) checked @endif> Yes</label>
                                        <label class="radio-inline mr-3"><input type="radio" name="display_adtitle_with_banner" value="0" @if($AdGroup->display_adtitle_with_banner == 0) checked @endif> No</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label" for="ad_view_id">Select Ad view
                                    </label>
                                    <select class="form-control" id="ad_view_id" name="ad_view_id">
                                        <option></option>
                                        @foreach($ad_views as $ad_view)
                                            <option value="{{ $ad_view->id  }}" @if($ad_view->id==$AdGroup->ad_view_id) selected @endif>{{ $ad_view->view_name.' (W: '.$ad_view->width.', H: '.$ad_view->height.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="banner_view">
                                    @if($AdGroup->ad_view_id == 1)
                                    <div class="form-group ">
                                        <label class="col-form-label" for="banner_view">Banner View
                                        </label>
                                        <div>
                                            <label class="radio-inline mr-3"><input type="radio" name="banner_view" value="1" @if($AdGroup->banner_view == 1) checked @endif> Horizontal</label>
                                            <label class="radio-inline mr-3"><input type="radio" name="banner_view" value="2" @if($AdGroup->banner_view == 2) checked @endif> Vertical</label>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </form>

                            <div id="groupInfoBox">
                                <?php $cnt_bannerimg = 1; ?>
                                @foreach($AdBanners as $AdBanner)
                                <div class="adGroupDataBox">
                                    <form class="form-valide AdBannerForm" action="" method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="col-form-label" for="AdTitle">Ad Title {{ $cnt_bannerimg }} <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control input-flat" id="ad_title" name="ad_title" placeholder="" value="{{ $AdBanner->ad_title }}">
                                            <label id="ad_title-error" class="error invalid-feedback animated fadeInDown" for="ad_title"></label>
                                        </div>

                                        <div class="form-group">
                                            <label>Ad Banner {{ $cnt_bannerimg }} <span class="text-danger">*</span></label>
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <input type="file" name="files[]" id="AdBannerFiles-{{ $cnt_bannerimg }}" multiple="multiple">
                                                    <input type="hidden" name="AdBannerImg" id="AdBannerImg-{{ $cnt_bannerimg }}" value="{{ $AdBanner->image }}">
                                                    <label id="AdBannerImg-error" class="error invalid-feedback animated fadeInDown" for="AdBannerImg"></label>
                                                    <?php
                                                    $script_html = '';
                                                    if($cnt_bannerimg != 1){
                                                    $script_html = '<script type="text/javascript">
                                                        var ImageUrl = $("#web_url").val() + "/admin/";
                                                        jQuery(document).ready(function() {
                                                            jQuery("#AdBannerFiles-'.$cnt_bannerimg.'").filer({
                                                                limit: 1,
                                                                maxSize: null,
                                                                extensions: ["jpg", "jpeg", "png"],
                                                                changeInput: \'<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&Drop files here</h3> <span style="display:inline-block; margin: 15px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>\',
                                                                showThumbs: true,
                                                                theme: "dragdropbox",
                                                                templates: {
                                                                    box: \'<ul class="jFiler-items-list jFiler-items-grid"></ul>\',
                                                                    item: \'<li class="jFiler-item">\
                                                                                <div class="jFiler-item-container">\
                                                                                    <div class="jFiler-item-inner">\
                                                                                        <div class="jFiler-item-thumb">\
                                                                                        <div class="jFiler-item-status"></div>\
                                                                                        <div class="jFiler-item-thumb-overlay">\
                                                                                            <div class="jFiler-item-info">\
                                                                                                <div style="display:table-cell;vertical-align: middle;">\
                                                                                                    <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
                                                                                                    <span class="jFiler-item-others">{{fi-size2}}</span>\
                                                                                                </div>\
                                                                                            </div>\
                                                                                        </div>\
                                                                                        {{fi-image}}\
                                                                                    </div>\
                                                                                    <div class="jFiler-item-assets jFiler-row">\
                                                                                        <ul class="list-inline pull-left">\
                                                                                            <li>{{fi-progressBar}}</li>\
                                                                                        </ul>\
                                                                                        <ul class="list-inline pull-right">\
                                                                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                                                                        </ul>\
                                                                                    </div>\
                                                                                </div>\
                                                                            </div>\
                                                                        </li>\',
                                                                    itemAppend: \'<li class="jFiler-item">\
                                                                                <div class="jFiler-item-container">\
                                                                                <div class="jFiler-item-inner">\
                                                                                    <div class="jFiler-item-thumb">\
                                                                                        <div class="jFiler-item-status"></div>\
                                                                                        <div class="jFiler-item-thumb-overlay">\
                                                                                            <div class="jFiler-item-info">\
                                                                                                <div style="display:table-cell;vertical-align: middle;">\
                                                                                                    <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name}}</b></span>\
                                                                                                    <span class="jFiler-item-others">{{fi-size2}}</span>\
                                                                                                </div>\
                                                                                            </div>\
                                                                                        </div>\
                                                                                        {{fi-image}}\
                                                                                    </div>\
                                                                                    <div class="jFiler-item-assets jFiler-row">\
                                                                                        <ul class="list-inline pull-left">\
                                                                                            <li><span class="jFiler-item-others">{{fi-icon}}</span></li>\
                                                                                        </ul>\
                                                                                        <ul class="list-inline pull-right">\
                                                                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                                                                        </ul>\
                                                                                    </div>\
                                                                                </div>\
                                                                            </div>\
                                                                        </li>\',
                                                                    progressBar: \'<div class="bar"></div>\',
                                                                    itemAppendToEnd: true,
                                                                    canvasImage: true,
                                                                    removeConfirmation: true,
                                                                    _selectors: {
                                                                        list: \'.jFiler-items-list\',
                                                                        item: \'.jFiler-item\',
                                                                        progressBar: \'.bar\',
                                                                        remove: \'.jFiler-item-trash-action\'
                                                                    }
                                                                },
                                                                dragDrop: {
                                                                    dragEnter: null,
                                                                    dragLeave: null,
                                                                    drop: null,
                                                                    dragContainer: null,
                                                                },
                                                                appendTo: "#adBannerBox-'.$cnt_bannerimg.'",
                                                                uploadFile: {
                                                                    url: ImageUrl+"adgroups/uploadfile?action=uploadAdBannerImg",
                                                                    data: { \'_token\': jQuery(\'meta[name="csrf-token"]\').attr(\'content\') },
                                                                    type: \'POST\',
                                                                    enctype: \'multipart/form-data\',
                                                                    synchron: true,
                                                                    beforeSend: function () {
                                                                    },
                                                                    success: function (res, itemEl, listEl, boxEl, newInputEl, inputEl, id) {
                                                                            var parent = itemEl.find(".jFiler-jProgressBar").parent(),
                                                                            new_file_name = res.data,
                                                                            filerKit = inputEl.prop("jFiler");
                                                                            jQuery("#AdBannerImg-'.$cnt_bannerimg.'").val(new_file_name);
                                                                            filerKit.files_list[id].name = new_file_name;

                                                                            itemEl.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                                                                                jQuery("<div class=\'jFiler-item-others text-success\'><i class=\'icon-jfi-check-circle\'></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
                                                                            });
                                                                    },
                                                                    error: function (el) {
                                                                        var parent = el.find(".jFiler-jProgressBar").parent();
                                                                        el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                                                                            jQuery("<div class=\'jFiler-item-others text-error\'><i class=\'icon-jfi-minus-circle\'></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
                                                                        });
                                                                    },
                                                                    statusCode: null,
                                                                    onProgress: null,
                                                                    onComplete: null
                                                                },
                                                                files: null,
                                                                addMore: false,
                                                                allowDuplicates: true,
                                                                clipBoardPaste: true,
                                                                excludeName: null,
                                                                beforeRender: null,
                                                                afterRender: null,
                                                                beforeShow: null,
                                                                beforeSelect: null,
                                                                onSelect: null,
                                                                afterShow: null,
                                                                onRemove: function (itemEl, file, id, listEl, boxEl, newInputEl, inputEl) {
                                                                    var filerKit = inputEl.prop("jFiler"),
                                                                    file_name = filerKit.files_list[id].name;
                                                                    var removableFile = jQuery("#AdBannerImg-'.$cnt_bannerimg.'").val();
                                                                    jQuery.post(ImageUrl+\'adgroups/removefile?action=removeAdBannerImg\', {\'_token\': $(\'meta[name="csrf-token"]\').attr(\'content\'),file: removableFile});
                                                                    jQuery("#AdBannerImg-'.$cnt_bannerimg.'").removeAttr(\'value\');
                                                                },
                                                            onEmpty: null,
                                                            options: null,
                                                            dialogs: {
                                                                alert: function (text) {
                                                                    return alert(text);
                                                                },
                                                                confirm: function (text, callback) {
                                                                    confirm(text) ? callback() : null;
                                                                }
                                                            },
                                                            captions: {
                                                                button: "Choose Files",
                                                                feedback: "Choose files To Upload",
                                                                feedback2: "files were chosen",
                                                                drop: "Drop file here to Upload",
                                                                removeConfirmation: "Are you sure you want to remove this file?",
                                                                errors: {
                                                                    filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
                                                                    filesType: "Only Images are allowed to be uploaded.",
                                                                    filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-fileMaxSize}} MB.",
                                                                    filesSizeAll: "Files you\'ve choosed are too large! Please upload files up to {{fi-maxSize}} MB."
                                                                }
                                                            }
                                                        });
                                                        });
                                                        </script>';
                                                    } ?>
                                                    {!! $script_html !!}
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                                    <div id="adBannerBox-{{ $cnt_bannerimg }}" class="uploadedImgBox">
                                                        <div class="jFiler-items jFiler-row oldImgDisplayBox">
                                                            <ul class="jFiler-items-list jFiler-items-grid">
                                                                <li class="jFiler-item" data-jfiler-index="1" style="" id="ImgBox-{{ $cnt_bannerimg }}">
                                                                    <div class="jFiler-item-container">
                                                                        <div class="jFiler-item-inner">
                                                                            <div class="jFiler-item-thumb">
                                                                                <div class="jFiler-item-status"></div>
                                                                                <div class="jFiler-item-thumb-image"><img src="{{ url('public/'.$AdBanner->image) }}" draggable="false"></div>
                                                                            </div>
                                                                            <div class="jFiler-item-assets jFiler-row">
                                                                                <ul class="list-inline pull-right">
                                                                                    <li><a class="icon-jfi-trash jFiler-item-trash-action" onclick="removeuploadedimg('ImgBox-{{ $cnt_bannerimg }}', 'AdBannerImg-{{ $cnt_bannerimg }}','AdBannerFiles-{{ $cnt_bannerimg }}');"></a></li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-form-label" for="application_dropdown_id">Ad Type {{ $cnt_bannerimg }}
                                            </label>
                                            <select class="form-control AdBannerInfo" id="application_dropdown_id" name="application_dropdown_id" data-id="{{ $cnt_bannerimg }}">
                                                @foreach($application_dropdowns as $application_dropdown)
                                                    <option value="{{ $application_dropdown->id  }}" @if($application_dropdown->id==$AdBanner->application_dropdown_id) selected @endif>{{ $application_dropdown->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="AdBannerInfoBox">
                                            @if($AdBanner->application_dropdown_id == 3)
                                            <div class="form-group">
                                                <label class="col-form-label" for="underPrice">Price  <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control input-flat" id="value" name="value" value="{{ $AdBanner->value }}">
                                                <label id="value-error" class="error invalid-feedback animated fadeInDown" for="value"></label>
                                            </div>
                                            @endif
                                            @if($AdBanner->application_dropdown_id == 5)
                                            <div class="form-group">
                                                <label class="col-form-label" for="category">Select Category
                                                </label>
                                                <select id="value_{{ $cnt_bannerimg }}" name="value" class="category_dropdown_catalog category_dropdown" data-id="{{ $cnt_bannerimg }}">
                                                    @foreach($categories_catalog_arr as $category)
                                                        <option value="{{ $category['id']  }}" @if($category['id'] == $AdBanner->value) selected @endif>{{ $category['category_name'] }}</option>
                                                    @endforeach
                                                </select>
                                                <label id="value-error" class="error invalid-feedback animated fadeInDown" for="value"></label>
                                            </div>
                                            @endif
                                            @if($AdBanner->application_dropdown_id == 7)
                                                <div class="form-group" id="category_dropdown">
                                                    <label class="col-form-label" for="category">Select Category
                                                    </label>
                                                    <select id="value_{{ $cnt_bannerimg }}" name="value" data-id="{{ $cnt_bannerimg }}" class="category_dropdown">
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id  }}" @if($category->id == $AdBanner->value) selected @endif>{{ $category->category_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label id="value-error" class="error invalid-feedback animated fadeInDown" for="value"></label>
                                                </div>
                                            @endif
                                            @if($AdBanner->application_dropdown_id == 10)
                                            <div class="form-group">
                                                <label class="col-form-label" for="arrivalDays">Days</label>
                                                <input type="number" class="form-control input-flat" id="value" name="value" value="{{ $AdBanner->value }}">
                                                <label id="value-error" class="error invalid-feedback animated fadeInDown" for="value"></label>
                                            </div>
                                            @endif
                                            @if($AdBanner->application_dropdown_id == 14)
                                            <div class="form-group">
                                                <label class="col-form-label" for="bannerUrl">Banner URL</label>
                                                <input type="text" class="form-control input-flat" id="value" name="value" value="{{ $AdBanner->value }}">
                                                <label id="value-error" class="error invalid-feedback animated fadeInDown" for="value"></label>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="productDropdownBox">
                                            @if($AdBanner->application_dropdown_id == 5)
                                                <?php $variants_arr = getproducts($AdBanner->value); ?>
                                                <div class="form-group" id="">
                                                    <label class="col-form-label" for="product">Select Product</label>
                                                    <select id="product_{{ $cnt_bannerimg }}" name="product" class="product_dropdown">
                                                        <option></option>
                                                        @foreach($variants_arr as $variant)
                                                            <option value="{{ $variant['id']  }}" @if($variant['id'] == $AdBanner->product_variant_id) selected @endif>{{ $variant['product_title'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div id="product-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                                <?php $cnt_bannerimg++; ?>
                                @endforeach
                            </div>

                            <div class="row pull-right pr-4">
                                <button type="button" class="btn btn-outline-primary mr-2" id="AddMoreBannerBtn">Add More <span class="btn-icon-right p-0" style="border-left: unset"><i class="fa fa-plus"></i></span></button>
                            </div>
                            <br>
                            <div class="row pl-3">
                                <button type="button" id="SubmitAdGroupBtn" name="SubmitAdGroupBtn" class="btn btn-primary">Submit <i class="fa fa-circle-o-notch fa-spin submitloader" style="display:none;"></i></button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    @include('admin.adgroups.adgroup_js')
@endsection
