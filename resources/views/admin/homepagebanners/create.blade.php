<form class="form-valide" action="" id="BannerCreateForm" method="post" enctype="multipart/form-data">

    <div id="attr-cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}
    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 container justify-content-center">
    <div class="form-group">
        <label class="col-form-label" for="Serial_No">Serial No <span class="text-danger">*</span>
        </label>
        <input type="number" class="form-control input-flat" id="sr_no" name="sr_no" placeholder="" value="{{ isset($sr_no)?$sr_no+1:1 }}">
        <div id="srno-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <label class="col-form-label" for="Banner">Banner  <span class="text-danger">*</span>
        </label>
        <input type="file" name="files[]" id="BannerFiles" multiple="multiple">
        <input type="hidden" name="BannerImg" id="BannerImg" value="">
        <div id="BannerImg-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
    </div>

    <div class="form-group">
        <select class="form-control" id="BannerInfo" name="BannerInfo">
            @foreach($application_dropdowns as $application_dropdown)
            <option value="{{ $application_dropdown->id }}" @if($application_dropdown->id==1) selected @endif>{{ $application_dropdown->title }}</option>
            @endforeach
        </select>
    </div>

    <div id="infoBox" class=""></div>
    <div id="productDropdownBox" class="pb-2"></div>

    {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
    <button type="button" class="btn btn-outline-primary" id="save_newBannerBtn" data-action="add">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
    <button type="button" class="btn btn-primary" id="save_closeBannerBtn" data-action="add">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>

    </div>
</form>

