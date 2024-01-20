<form class="form-valide" action="" id="FaqForm" method="post" enctype="multipart/form-data">
    <div id="attr-cover-spin" class="cover-spin"></div>
    {{ csrf_field() }}
    <input type="hidden" name="faq_id" value="{{ isset($Faq)?($Faq->id):'' }}">

    <div class="col-lg-6 col-md-8 col-sm-10 col-xs-12 container justify-content-center">
        <div class="form-group">
            <label class="col-form-label" for="Question">Question <span class="text-danger">*</span>
            </label>
            <input type="text" class="form-control input-flat" id="question" name="question" placeholder="" value="{{ $Faq->question }}">
            <div id="question-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <div class="form-group">
            <label class="col-form-label" for="Answer">Answer <span class="text-danger">*</span>
            </label>
            <textarea class="summernote" id="answer" name="answer">{!! $Faq->answer !!}</textarea>
            {{--            <textarea class="ckeditor form-control" id="answer" name="answer" placeholder=""></textarea>--}}
            <div id="answer-error" class="invalid-feedback animated fadeInDown" style="display: none;"></div>
        </div>

        <button type="button" class="btn btn-outline-primary" id="save_newFaqBtn" data-action="update">Save & New <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>&nbsp;&nbsp;
        <button type="button" class="btn btn-primary" id="save_closeFaqBtn" data-action="update">Save & Close <i class="fa fa-circle-o-notch fa-spin loadericonfa" style="display:none;"></i></button>
    </div>
</form>
