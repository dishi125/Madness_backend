<form class="form-valide" action="" id="BannerCreateForm" method="post" enctype="multipart/form-data">

    <div id="attr-cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}
    <input type="hidden" name="banner_id" value="{{ isset($banner)?($banner->id):'' }}">

    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 container justify-content-center">
        <div class="form-group">
            <label class="col-form-label" for="Serial_No">Serial No <span class="text-danger">*</span>
            </label>
            <input type="number" class="form-control input-flat" id="sr_no" name="sr_no" placeholder="" value="{{ $banner->sr_no }}">
            <div id="srno-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="Banner">Banner  <span class="text-danger">*</span>
            </label>
            <input type="file" name="files[]" id="BannerFiles" multiple="multiple">
            <input type="hidden" name="BannerImg" id="BannerImg" value="{{ isset($banner)?($banner->image):'' }}">
            <?php
            if( isset($banner) && isset($banner->image) ){
            ?>
            <div class="jFiler-items jFiler-row oldImgDisplayBox">
                <ul class="jFiler-items-list jFiler-items-grid">
                    <li id="ImgBox" class="jFiler-item" data-jfiler-index="1" style="">
                        <div class="jFiler-item-container">
                            <div class="jFiler-item-inner">
                                <div class="jFiler-item-thumb">
                                    <div class="jFiler-item-status"></div>
                                    <div class="jFiler-item-thumb-overlay"></div>
                                    <div class="jFiler-item-thumb-image"><img src="{{ url('public/'.$banner->image) }}" draggable="false"></div>
                                </div>
                                <div class="jFiler-item-assets jFiler-row">
                                    <ul class="list-inline pull-right">
                                        <li><a class="icon-jfi-trash jFiler-item-trash-action" onclick="removeuploadedimg('ImgBox', 'BannerImg','<?php echo $banner->image;?>');"></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <?php } ?>
            <div id="BannerImg-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <div class="form-group">
            <select class="form-control" id="BannerInfo" name="BannerInfo">
                @foreach($application_dropdowns as $application_dropdown)
                    <option value="{{ $application_dropdown->id }}" @if($application_dropdown->id == $banner->application_dropdown_id) selected @endif>{{ $application_dropdown->title }}</option>
                @endforeach
            </select>
        </div>

        <div id="infoBox" class="">
            @if($banner->application_dropdown_id == 3)
                <div class="form-group">
                    <label class="col-form-label" for="underPrice">Price  <span class="text-danger">*</span></label>
                    <input type="number" class="form-control input-flat" id="value" name="value" value="{{ $banner->value }}">
                    <div id="value-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                </div>
            @elseif($banner->application_dropdown_id == 5)
                <div class="form-group" id="category_dropdown">
                    <label class="col-form-label" for="category">Select Category</label>
                    <select id="value" name="value" class="category_dropdown_catalog">
                        <option></option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if($category->id == $banner->value) selected @endif>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    <div id="value-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                </div>
            @elseif($banner->application_dropdown_id == 7)
                <div class="form-group" id="category_dropdown">
                    <label class="col-form-label" for="category">Select Category</label>
                    <select id="value" name="value" class="">
                        <option></option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @if($category->id == $banner->value) selected @endif>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    <div id="value-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                </div>
            @elseif($banner->application_dropdown_id == 10)
                <div class="form-group">
                    <label class="col-form-label" for="arrivalDays">Days</label>
                    <input type="number" class="form-control input-flat" id="value" name="value" value="{{ $banner->value }}">
                    <div id="value-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                </div>
            @elseif($banner->application_dropdown_id == 14)
                <div class="form-group">
                    <label class="col-form-label" for="bannerUrl">Banner URL</label>
                    <input type="text" class="form-control input-flat" id="value" name="value" value="{{ $banner->value }}">
                    <div id="value-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                </div>
            @endif
        </div>

        <div id="productDropdownBox" class="pb-2">
            @if($banner->application_dropdown_id == 5)
                <div class="form-group" id="">
                    <label class="col-form-label" for="product">Select Product</label>
                    <select id="product" name="product" class="">
                        <option></option>
                        @foreach($products as $product)
                            <option value="{{ $product['id'] }}" @if($product['id'] == $banner->product_variant_id) selected @endif>{{ $product['product_title'] }}</option>
                        @endforeach
                    </select>
                    <div id="product-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
                </div>
            @endif
        </div>

        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
        <button type="button" class="btn btn-outline-primary" id="save_newBannerBtn" data-action="update">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
        <button type="button" class="btn btn-primary" id="save_closeBannerBtn" data-action="update">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>

    </div>
</form>

