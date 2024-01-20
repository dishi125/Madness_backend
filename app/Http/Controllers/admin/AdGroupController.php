<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdBanner;
use App\Models\AdGroup;
use App\Models\AdView;
use App\Models\ApplicationDropdown;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProjectPage;
use Illuminate\Http\Request;

class AdGroupController extends Controller
{
    private $page = "Ad Groups";

    public function index(){
        return view('admin.adgroups.list')->with('page',$this->page);
    }

    public function create(){
        $categories = Category::where('estatus',1)->orderBy('created_at','DESC')->get();
        $ad_views = AdView::where('estatus',1)->get();
        $application_dropdowns = ApplicationDropdown::get();

        return view('admin.adgroups.create',compact('categories','ad_views','application_dropdowns'))->with('page',$this->page);
    }

    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadAdBannerImg'){
            if ($request->hasFile('files')) {
                $image = $request->file('files')[0];
                $image_name = 'AdBanner_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/AdBanner');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/AdBanner/'.$image_name]);
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeAdBannerImg'){
            $image = $request->file;

            if(isset($image)) {
                $image = public_path($request->file);
                if (file_exists($image)) {
                    unlink($image);
                    return response()->json(['status' => '200']);
                }
            }
        }
    }

    public function addBannerForm(Request $request){
        $ad_view_id = $request->ad_view_id;
        if($ad_view_id==1 || $ad_view_id==2 || $ad_view_id==3){
            $limit = 1;
        }
        elseif ($ad_view_id==4 || $ad_view_id==5){
            $limit = 2;
        }
        elseif ($ad_view_id==6){
            $limit = 3;
        }
        elseif ($ad_view_id==7){
            $limit = 4;
        }

        $application_dropdowns = ApplicationDropdown::get();
        $html_application_dropdown_options = '';
        foreach ($application_dropdowns as $application_dropdown){
            $selected = '';
            if($application_dropdown->id==1){
                $selected = 'selected';
            }
            $html_application_dropdown_options .= '<option value="'.$application_dropdown->id.'"'.$selected.'>'.$application_dropdown->title.'</option>';
        }

        $html = "";
        $cnt_bannerimg = isset($request->last_form) ? $request->last_form + 1 : 1;
        for ($i=1; $i<=$limit; $i++){
            $html .= '<div class="adGroupDataBox">';
            $html .= '<form class="form-valide AdBannerForm" action="" method="post" enctype="multipart/form-data">';
            $html .= csrf_field();
            $html .= '<div class="form-group">
                        <label class="col-form-label" for="AdTitle">Ad Title '.$cnt_bannerimg.' <span class="text-danger">*</span></label>
                        <input type="text" class="form-control input-flat" id="ad_title" name="ad_title" placeholder="">
                        <label id="ad_title-error" class="error invalid-feedback animated fadeInDown" for="ad_title"></label>
                      </div>';
            $html .= '<div class="form-group">
                        <label>Ad Banner '.$cnt_bannerimg.' <span class="text-danger">*</span></label>
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                <input type="file" name="files[]" id="AdBannerFiles-'.$cnt_bannerimg.'" multiple="multiple">
                                <input type="hidden" name="AdBannerImg" id="AdBannerImg-'.$cnt_bannerimg.'" value="">
                                <label id="AdBannerImg-error" class="error invalid-feedback animated fadeInDown" for="AdBannerImg"></label>
                                <script type="text/javascript">
                                    var ImageUrl = $("#web_url").val() + "/admin/";
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
                                                data: {\'_token\': jQuery(\'meta[name="csrf-token"]\').attr(\'content\')},
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
                                                        jQuery("<div class=\"jFiler-item-others text-success\"><i class=\"icon-jfi-check-circle\"></i> Success</div>").hide().appendTo(parent).fadeIn("slow");
                                                    });
                                                },
                                                error: function (el) {
                                                    var parent = el.find(".jFiler-jProgressBar").parent();
                                                    el.find(".jFiler-jProgressBar").fadeOut("slow", function(){
                                                        jQuery("<div class=\"jFiler-item-others text-error\"><i class=\"icon-jfi-minus-circle\"></i> Error</div>").hide().appendTo(parent).fadeIn("slow");
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
                                        onRemove: function(itemEl, file, id, listEl, boxEl, newInputEl, inputEl){
                                            var filerKit = inputEl.prop("jFiler"),
                                                file_name = filerKit.files_list[id].name;
                                            var removableFile = jQuery("#AdBannerImg-'.$cnt_bannerimg.'").val();
                                            jQuery.post(ImageUrl+\'adgroups/removefile?action=removeAdBannerImg\', {\'_token\': $(\'meta[name="csrf-token"]\').attr(\'content\'), file: removableFile});
                                            jQuery("#AdBannerImg-'.$cnt_bannerimg.'").removeAttr(\'value\');
                                        },
                                        onEmpty: null,
                                        options: null,
                                        dialogs: {
                                            alert: function(text) {
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
                                                filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
                                                filesSizeAll: "Files you\'ve choosed are too large! Please upload files up to {{fi-maxSize}} MB."
                                            }
                                        }
                                    });
                                </script>
                            </div>
                            <div class="col-lg-8 col-md-8 col-sm-6 col-xs-12">
                                <div id="adBannerBox-'.$cnt_bannerimg.'" class="uploadedImgBox"></div>
                            </div>
                        </div>
                      </div>';
            $html .= '<div class="form-group">
                        <label class="col-form-label" for="ad_view_id">Ad Type '.$cnt_bannerimg.'</label>
                        <select class="form-control AdBannerInfo" id="application_dropdown_id" name="application_dropdown_id" data-id="'.$cnt_bannerimg.'">'.$html_application_dropdown_options.'</select>
                      </div>';
            $html .= '<div class="AdBannerInfoBox"></div>';
            $html .= '<div class="productDropdownBox"></div>';
            $html .= '</form>';
            $html .= '</div>';

            $cnt_bannerimg++;
        }

        return ['html' => $html];
    }

    public function save(Request $request){
//        $myValue = array();
//        parse_str($request['AdBannerForm1'],$myValue);
//        dd($request->all(),$myValue);

        $total_AdBannerForm = $request->total_AdBannerForm;

        $adGroup = new AdGroup();

        $adGroup_old_images = array();
        if (isset($request->action) && $request->action=="edit"){
            $adGroup = AdGroup::find($request->adgroup_id);
            $adBanners = AdBanner::where('ad_group_id',$request->adgroup_id)->get();
            foreach ($adBanners as $adBanner){
                array_push($adGroup_old_images,$adBanner->image);
                $adBanner->estatus = 3;
                $adBanner->save();
                $adBanner->delete();
            }
        }

        $adGroup->category_id = $request->category_id;
        $adGroup->group_title = $request->group_title;
        $adGroup->group_bg_color = $request->group_bg_color;
        $adGroup->display_adtitle_with_banner = $request->display_adtitle_with_banner;
        $adGroup->ad_view_id = $request->ad_view_id;
        $adGroup->banner_view = isset($request->banner_view) ? $request->banner_view : null;
        $adGroup->save();

        $not_removable_images = array();
        for ($i=1;$i<=$total_AdBannerForm;$i++) {
            $myValue = array();
            $str ="AdBannerForm".$i;
            parse_str($request[$str],$myValue);

            $adBanner = new AdBanner();
            $adBanner->ad_group_id = $adGroup->id;
            $adBanner->ad_title = $myValue['ad_title'];
            $adBanner->image = $myValue['AdBannerImg'];
            if(in_array($myValue['AdBannerImg'],$adGroup_old_images)){
                array_push($not_removable_images,$myValue['AdBannerImg']);
            }
            $adBanner->application_dropdown_id = $myValue['application_dropdown_id'];
            if(isset($myValue['value']) && $myValue['value']!=""){
                $adBanner->value = $myValue['value'];
            }
            if(isset($myValue['product']) && $myValue['product']!=""){
                $adBanner->product_variant_id = $myValue['product'];
            }
            $adBanner->save();
        }

        foreach ($adGroup_old_images as $adGroup_old_image){
            if (!in_array($adGroup_old_image,$not_removable_images) && $request->action=="edit"){
                $image = public_path($adGroup_old_image);
                if (file_exists($image)) {
                    unlink($image);
                }
            }
        }

        return ['status' => 200];
    }

    public function getBannerInfoVal(Request $request){
        $data = getDropdownInfoVal($request->AdBannerInfo);

        $categories_catalog = Category::where('estatus',1)->orderBy('created_at','DESC')->get();
        $categories_catalog_arr = array();
        foreach ($categories_catalog as $category_catalog) {
            $products1 = Product::where('subchild_category_id', $category_catalog->id)->where('estatus', 1)->count();
            $products2 = Product::where('child_category_id', $category_catalog->id)->where('subchild_category_id', null)->where('estatus', 1)->count();
            $products_cnt = $products1 + $products2;
            if ($products_cnt > 0){
                array_push($categories_catalog_arr,$category_catalog);
            }
        }

        return ["html" => $data['html'], 'categories' => $data['categories'], 'categories_catalog' => $categories_catalog_arr];
    }

    public function getproducts($cat_id){
        $variants_arr = getproducts($cat_id);
        return $variants_arr;
    }

    public function alladgroupslist(Request $request){
        if ($request->ajax()) {
            $tab_type = $request->tab_type;
            if ($tab_type == "active_adgroup_tab"){
                $estatus = 1;
            }
            elseif ($tab_type == "deactive_adgroup_tab"){
                $estatus = 2;
            }

            $columns = array(
                0 =>'id',
                1 =>'category_id',
                2=> 'ad_view_id',
                3=> 'estatus',
                4=> 'created_at',
                5=> 'action',
            );

            $totalData = AdGroup::count();
            if (isset($estatus)){
                $totalData = AdGroup::where('estatus',$estatus)->count();
            }

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $adGroups = AdGroup::with('category','adview','adbanner.applicationdropdown')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                if (isset($estatus)){
                    $adGroups = AdGroup::with('category','adview','adbanner.applicationdropdown')
                            ->where('estatus',$estatus)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
                }
            }
            else {
                $search = $request->input('search.value');
                $adGroups = AdGroup::with('category','adview','adbanner.applicationdropdown');
                if (isset($estatus)){
                    $adGroups = $adGroups->where('estatus',$estatus);
                }
                $adGroups = $adGroups->where(function($mainQuery) use($search){
                    $mainQuery->whereHas('category',function ($Query) use($search) {
                            $Query->where('category_name', 'Like', '%' . $search . '%');
                        })
                        ->orWhereHas('adview',function ($Query) use($search) {
                            $Query->where('view_name', 'Like', '%' . $search . '%');
                        });
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($adGroups->toArray());
            }

            $data = array();

            if(!empty($adGroups))
            {
                foreach ($adGroups as $adGroup)
                {
                    $page_id = ProjectPage::where('route_url','admin.adgroups.list')->pluck('id')->first();

                    if( $adGroup->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="AdGroupstatuscheck_'. $adGroup->id .'" onchange="changeAdGroupStatus('. $adGroup->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($adGroup->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="AdGroupstatuscheck_'. $adGroup->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $adGroup->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="AdGroupstatuscheck_'. $adGroup->id .'" onchange="changeAdGroupStatus('. $adGroup->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($adGroup->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="AdGroupstatuscheck_'. $adGroup->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editAdGroupBtn" class="btn btn-gray text-blue btn-sm" onclick="" data-id="' .$adGroup->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteAdGroupBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteAdGroupModal" onclick="" data-id="' .$adGroup->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $table = '<table class="subclass text-center" cellpadding="6" cellspacing="0" border="0" style="padding-left:50px; width: 100%">';
                    $table .= '<tr>
                                <th>Thumb</th>
                                <th>Ad Title</th>
                                <th>Ad Type</th>
                                <th>Ad Target</th>
                               </tr>';
                    foreach ($adGroup->adbanner as $adbanner){
                        $value = "";
                        if ($adbanner->application_dropdown_id == 3){
                            $value = "Price: ".$adbanner->value;
                        }
                        if ($adbanner->application_dropdown_id == 5){
                            $product_variant_value = ProductVariant::find($adbanner->product_variant_id);
                            if($product_variant_value) {
                                $value = "Product: " . $product_variant_value->product_title;
                            }
                        }
                        if ($adbanner->application_dropdown_id == 7){
                            $category_value = Category::find($adbanner->value);
                            $value = "Category: ".$category_value->category_name;
                        }
                        if ($adbanner->application_dropdown_id == 10){
                            $value = "Arrival Days: ".$adbanner->value;
                        }
                        if ($adbanner->application_dropdown_id == 14){
                            $value = "URL: ".$adbanner->value;
                        }
                        $table .= '<tr>
                                    <td><img src="'.url('public/'.$adbanner->image).'" width="50px" height="50px"></td>
                                    <td>'.$adbanner->ad_title.'</td>
                                    <td>'.$adbanner->applicationdropdown->title.'</td>
                                    <td>'.$value.'</td>
                                   </tr>';
                    }
                    $table .= '</table>';

                    $nestedData['category_id'] = ($adGroup->category_id == 0) ? "Home Page" : $adGroup->category->category_name;
                    $nestedData['ad_view_id'] = $adGroup->adview->view_name;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($adGroup->created_at));
                    $nestedData['action'] = $action;
                    $nestedData['table1'] = $table;
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

            echo json_encode($json_data);
        }
    }

    public function changeAdGroupstatus($id){
        $AdGroup = AdGroup::find($id);
        if ($AdGroup->estatus==1){
            $AdGroup->estatus = 2;
            $AdGroup->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($AdGroup->estatus==2){
            $AdGroup->estatus = 1;
            $AdGroup->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function deleteadgroup($id){
        $AdGroup = AdGroup::find($id);
        if ($AdGroup){
            $Adbanners = AdBanner::where('ad_group_id',$id)->get();
            foreach ($Adbanners as $Adbanner){
                if(file_exists(public_path($Adbanner->image))){
                    unlink(public_path($Adbanner->image));
                }
                $Adbanner->estatus = 3;
                $Adbanner->save();
                $Adbanner->delete();
            }

            $AdGroup->estatus = 3;
            $AdGroup->save();
            $AdGroup->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function editadgroup($id){
        $categories = Category::where('estatus',1)->orderBy('created_at','DESC')->get();
        $ad_views = AdView::where('estatus',1)->get();
        $application_dropdowns = ApplicationDropdown::get();

        $categories_catalog = Category::where('estatus',1)->orderBy('created_at','DESC')->get();
        $categories_catalog_arr = array();
        foreach ($categories_catalog as $category_catalog) {
            $products1 = Product::where('subchild_category_id', $category_catalog->id)->where('estatus', 1)->count();
            $products2 = Product::where('child_category_id', $category_catalog->id)->where('subchild_category_id', null)->where('estatus', 1)->count();
            $products_cnt = $products1 + $products2;
            if ($products_cnt > 0){
                array_push($categories_catalog_arr,$category_catalog);
            }
        }

        $AdGroup = AdGroup::find($id);
        $AdBanners = AdBanner::where('ad_group_id',$id)->get();
        return view('admin.adgroups.edit',compact('categories','ad_views','application_dropdowns','categories_catalog_arr','AdGroup','AdBanners'))->with('page',$this->page);
    }
}
