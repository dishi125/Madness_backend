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
                            Add Ad Banners
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
                                <div class="form-group">
                                    <label class="col-form-label" for="Page">Select Page  <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" id="category_id" name="category_id">
                                        <option></option>
                                        <option value="0">Home Page</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id  }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                    <label id="category_id-error" class="error invalid-feedback animated fadeInDown" for="category_id"></label>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label" for="GroupTitle">Group Title  <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control input-flat" id="group_title" name="group_title" placeholder="">
                                    <label id="group_title-error" class="error invalid-feedback animated fadeInDown" for="group_title"></label>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label" for="GroupBackgroundColor">Group Background Color  <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="colorpicker form-control input-flat" value="" id="group_bg_color" name="group_bg_color">
                                    <label id="group_bg_color-error" class="error invalid-feedback animated fadeInDown" for="group_bg_color"></label>
                                </div>

                                <div class="form-group ">
                                    <label class="col-form-label" for="display_adtitle_with_banner">Do you wish to display AD Title with Banner?
                                    </label>
                                    <div>
                                        <label class="radio-inline mr-3"><input type="radio" name="display_adtitle_with_banner" value="1" checked> Yes</label>
                                        <label class="radio-inline mr-3"><input type="radio" name="display_adtitle_with_banner" value="0"> No</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-form-label" for="ad_view_id">Select Ad view
                                    </label>
                                    <select class="form-control" id="ad_view_id" name="ad_view_id">
                                        <option></option>
                                        @foreach($ad_views as $ad_view)
                                            <option value="{{ $ad_view->id  }}" @if($ad_view->id==1) selected @endif>{{ $ad_view->view_name.' (W: '.$ad_view->width.', H: '.$ad_view->height.')' }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="banner_view">
                                    <div class="form-group ">
                                        <label class="col-form-label" for="banner_view">Banner View
                                        </label>
                                        <div>
                                            <label class="radio-inline mr-3"><input type="radio" name="banner_view" value="1" checked> Horizontal</label>
                                            <label class="radio-inline mr-3"><input type="radio" name="banner_view" value="2"> Vertical</label>
                                        </div>
                                    </div>
                                </div>
                                </form>

                                <div id="groupInfoBox">
                                    <div class="adGroupDataBox">
                                    <form class="form-valide AdBannerForm" action="" method="post" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        <div class="form-group">
                                            <label class="col-form-label" for="AdTitle">Ad Title 1 <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" class="form-control input-flat" id="ad_title" name="ad_title" placeholder="">
                                            <label id="ad_title-error" class="error invalid-feedback animated fadeInDown" for="ad_title"></label>
                                        </div>

                                        <div class="form-group">
                                            <label>Ad Banner 1 <span class="text-danger">*</span></label>
                                            <div class="row">
                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <input type="file" name="files[]" id="AdBannerFiles-1" multiple="multiple">
                                                    <input type="hidden" name="AdBannerImg" id="AdBannerImg-1" value="">
                                                    <label id="AdBannerImg-error" class="error invalid-feedback animated fadeInDown" for="AdBannerImg"></label>
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                                    <div id="adBannerBox-1" class="uploadedImgBox"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-form-label" for="application_dropdown_id">Ad Type 1
                                            </label>
                                            <select class="form-control AdBannerInfo" id="application_dropdown_id" name="application_dropdown_id" data-id="1">
                                                @foreach($application_dropdowns as $application_dropdown)
                                                    <option value="{{ $application_dropdown->id  }}" @if($application_dropdown->id==1) selected @endif>{{ $application_dropdown->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="AdBannerInfoBox"></div>
                                        <div class="productDropdownBox"></div>
                                    </form>
                                    </div>
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
