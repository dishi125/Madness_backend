<form class="form-valide" action="" id="CategoryCreateForm" method="post" enctype="multipart/form-data">

    <div id="attr-cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}
    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 container justify-content-center">
    <input type="hidden" name="category_id" value="{{ isset($category)?($category->id):'' }}">
    <div class="form-group">
        <label class="col-form-label" for="Serial_No">Serial No <span class="text-danger">*</span>
        </label>
        <input type="number" class="form-control input-flat" id="sr_no" name="sr_no" placeholder="" value="{{ isset($category)?($category->sr_no):'' }}">
        <div id="srno-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="category_name">Category Name <span class="text-danger">*</span>
        </label>
        <input type="text" class="form-control input-flat" id="category_name" name="category_name" value="{{ isset($category)?($category->category_name):'' }}">
        <div id="categoryname-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    @if(isset($categories) && !empty($categories) && $category->parent_category_id!=0)
        <div class="form-group">
            <label class="col-form-label" for="parent_category_id">Parent Category
            </label>
            <select id='parent_category_id' name="parent_category_id">
                <option></option>
                @foreach($categories as $cat)
                    <option value="{{ $cat['id'] }}" @if(isset($category) && $category->parent_category_id == $cat['id']) selected @endif>{{ $cat['category_name'] }}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if(isset($attributes) && !empty($attributes))
    <div class="form-group" style="@if($is_sub_child==true || $category->parent_category_id==0) display: none @endif" id="attribute_variation">
        <label class="col-form-label" for="attribute_variation">Select Attribute for Variation
        </label>
        <select id='attribute_id_variation' name="attribute_id_variation" class="">
            <option></option>
            @foreach($attributes as $attr)
                <option value="{{ $attr['id'] }}" @if(isset($category) && $category->attribute_id_variation == $attr['id']) selected @endif>{{ $attr['attribute_name'] }}</option>
            @endforeach
        </select>
    </div>
    @endif

    @if(isset($specifications) && !empty($specifications))
    <div class="form-group" style="@if($is_sub_child==true || $category->parent_category_id==0) display: none @endif" id="attribute_required_specification">
        <label class="col-form-label" for="attribute_required_specification">Select Attribute for Required Specification
        </label>
        <?php $attribute_id_req_spec = explode(",",$category->attribute_id_req_spec); ?>
        <select id='attribute_id_req_spec' name="attribute_id_req_spec[]" class="" multiple>
            @foreach($specifications as $spec)
                <option value="{{ $spec['id'] }}" @if(isset($category) && in_array($spec['id'],$attribute_id_req_spec)) selected @endif>{{ $spec['attribute_name'] }}</option>
            @endforeach
        </select>
    </div>
    @endif

    @if(isset($specifications) && !empty($specifications))
    <div class="form-group" style="@if($is_sub_child==true || $category->parent_category_id==0) display: none @endif" id="attribute_optional_specification">
        <label class="col-form-label" for="attribute_optional_specification">Select Attribute for Optional Specification
        </label>
        <?php $attribute_id_opt_spec = explode(",",$category->attribute_id_opt_spec); ?>
        <select id='attribute_id_opt_spec' name="attribute_id_opt_spec[]" class="" multiple>
            @foreach($specifications as $spec)
                <option value="{{ $spec['id'] }}" @if(isset($category) && in_array($spec['id'],$attribute_id_opt_spec)) selected @endif>{{ $spec['attribute_name'] }}</option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="form-group" style="@if($is_sub_child==true || $category->parent_category_id==0) display: none @endif" id="order_return_days_textbox">
        <label class="col-form-label" for="order_return_days">Order Return Days
        </label>
        <input type="number" class="form-control input-flat" id="order_return_days" name="order_return_days" value="{{ $category->order_return_days }}">
        <div id="order_return_days-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="Thumbnail">Thumbnail  <span class="text-danger">*</span>
        </label>
        <input type="file" name="files[]" id="catIconFiles" multiple="multiple">
        <input type="hidden" name="catImg" id="catImg" value="{{ isset($category)?($category->category_thumb):'' }}">

        <?php
        if( isset($category) && isset($category->category_thumb) ){
        ?>
        <div class="jFiler-items jFiler-row oldImgDisplayBox">
            <ul class="jFiler-items-list jFiler-items-grid">
                <li id="ImgBox" class="jFiler-item" data-jfiler-index="1" style="">
                    <div class="jFiler-item-container">
                        <div class="jFiler-item-inner">
                            <div class="jFiler-item-thumb">
                                <div class="jFiler-item-status"></div>
                                <div class="jFiler-item-thumb-overlay"></div>
                                <div class="jFiler-item-thumb-image"><img src="{{ url('public/'.$category->category_thumb) }}" draggable="false"></div>
                            </div>
                            <div class="jFiler-item-assets jFiler-row">
                                <ul class="list-inline pull-right">
                                    <li><a class="icon-jfi-trash jFiler-item-trash-action" onclick="removeuploadedimg('ImgBox', 'catImg','<?php echo $category->category_thumb;?>');"></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <?php } ?>

        <div id="categorythumb-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>


    {{--                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
    <button type="button" class="btn btn-outline-primary" id="save_newCategoryBtn" data-action="update">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
    <button type="button" class="btn btn-primary" id="save_closeCategoryBtn" data-action="update">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
    </div>
</form>

