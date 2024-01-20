<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDropdown;
use App\Models\Category;
use App\Models\HomepageBanner;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomePageBannerController extends Controller
{
    private $page = "Slider";

    public function index(){
        $action = "list";
        return view('admin.homepagebanners.list',compact('action'))->with('page',$this->page);
    }

    public function create(){
        $action = "create";
        $sr_no = HomepageBanner::where('estatus',1)->orderBy('sr_no','desc')->pluck('sr_no')->first();
        $application_dropdowns = ApplicationDropdown::get();
        return view('admin.homepagebanners.list',compact('action','sr_no','application_dropdowns'))->with('page',$this->page);
    }

    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadBannerImg'){
            if ($request->hasFile('files')) {
                $image = $request->file('files')[0];
                $image_name = 'HomePageBanner_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/HomePageBanner');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/HomePageBanner/'.$image_name]);
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeBannerImg'){
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

    public function getBannerInfoVal(Request $request){
        $data = getDropdownInfoVal($request->bannerInfo);
        return ["html" => $data['html'], 'categories' => $data['categories']];
    }

    public function save(Request $request){
        $messages = [
            'sr_no.required' =>'Please provide valid Serial Number',
            'sr_no.numeric' =>'Please provide valid Serial Number',
            'BannerImg.required' =>'Please provide a Banner Image',
        ];

        $validator = Validator::make($request->all(), [
            'sr_no' => 'required|numeric',
            'BannerImg' => 'required',
        ], $messages);

        if($request->BannerInfo == 3 || $request->BannerInfo == 5 || $request->BannerInfo == 7 || $request->BannerInfo == 10 || $request->BannerInfo == 14){
            $messages = [
                'sr_no.required' =>'Please provide valid Serial Number',
                'sr_no.numeric' =>'Please provide valid Serial Number',
                'BannerImg.required' =>'Please provide a Banner Image',
            ];

            $validator = Validator::make($request->all(), [
                'sr_no' => 'required|numeric',
                'BannerImg' => 'required',
                'value' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if (isset($request->action) && $request->action=="update"){
            $action = "update";
            $banner = HomepageBanner::find($request->banner_id);

            if(!$banner){
                return response()->json(['status' => '400']);
            }

            if ($banner->image != $request->BannerImg){
                if(isset($banner->image)) {
                    $image = public_path($banner->image);
                    if (file_exists($image)) {
                        unlink($image);
                    }
                }
                $banner->image = $request->BannerImg;
            }

            $banner->sr_no = $request->sr_no;
            $banner->application_dropdown_id = $request->BannerInfo;
            $banner->value = $request->value;
            $banner->product_variant_id = isset($request->product) ? $request->product : null;
        }
        else{
            $action = "add";
            $banner = new HomepageBanner();
            $banner->sr_no = $request->sr_no;
            $banner->image = $request->BannerImg;
            $banner->application_dropdown_id = $request->BannerInfo;
            $banner->value = $request->value;
            $banner->product_variant_id = isset($request->product) ? $request->product : null;
            $banner->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }

        $banner->save();

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allbannerlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'sr_no',
                1 => 'image',
                2 => 'application_dropdown_id',
                3 => 'value',
                4 => 'estatus',
                5 => 'created_at',
                6 => 'action',
            );
            $totalData = HomepageBanner::count();
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "sr_no"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $banners = HomepageBanner::with('applicationdropdown')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $banners =  HomepageBanner::with('applicationdropdown')
                    ->where('sr_no','LIKE',"%{$search}%")
                    ->orWhereHas('applicationdropdown',function ($mainQuery) use($search) {
                        $mainQuery->where('title', 'Like', '%' . $search . '%');
                    })
                    ->orWhere('value','LIKE',"%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = HomepageBanner::where('sr_no','LIKE',"%{$search}%")
                    ->orWhereHas('applicationdropdown',function ($mainQuery) use($search) {
                        $mainQuery->where('title', 'Like', '%' . $search . '%');
                    })
                    ->orWhere('value','LIKE',"%{$search}%")
                    ->count();
            }

            $data = array();

            if(!empty($banners))
            {
                foreach ($banners as $banner)
                {
                    $page_id = ProjectPage::where('route_url','admin.homepagebanners.list')->pluck('id')->first();

                    if($banner->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="Bannerstatuscheck_'. $banner->id .'" onchange="changeBannerStatus('. $banner->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    else if ($banner->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="Bannerstatuscheck_'. $banner->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if($banner->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="Bannerstatuscheck_'. $banner->id .'" onchange="changeBannerStatus('. $banner->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    else if ($banner->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="Bannerstatuscheck_'. $banner->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editBannerBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$banner->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteBannerBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteBannerModal" onclick="" data-id="' .$banner->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $value = $banner->value;
                    if ($banner->application_dropdown_id == 5 || $banner->application_dropdown_id == 7){
                        $category = Category::where('id',$banner->value)->first();
                        $value = $category->category_name;
                    }

                    $img_path = url('public/'.$banner->image);
                    $nestedData['image'] = '<img src="'. $img_path .'" width="50px" height="50px" alt="Banner Image">';
                    $nestedData['application_dropdown_id'] = $banner->applicationdropdown->title;
                    $nestedData['value'] = $value;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($banner->created_at));
                    $nestedData['action'] = $action;
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

    public function editbanner($id){
        $action = "edit";
        $banner = HomepageBanner::find($id);
        $application_dropdowns = ApplicationDropdown::get();
        $categories = Category::where('estatus',1)->orderBy('created_at','DESC')->get();

        $products = "";
        if($banner->application_dropdown_id == 5) {
            $products = getproducts($banner->value);
        }

        return view('admin.homepagebanners.list',compact('action','banner','application_dropdowns','categories','products'))->with('page',$this->page);
    }

    public function deletebanner($id){
        $Banner = HomepageBanner::find($id);
        if ($Banner){
            $image = $Banner->image;
            $Banner->estatus = 3;
            $Banner->save();

            $Banner->delete();
            $image = public_path($image);
            if (file_exists($image)) {
                unlink($image);
            }
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function getproducts($cat_id){
        $variants_arr = getproducts($cat_id);
        return $variants_arr;
    }

    public function changeBannerStatus($id){
        $Banner = HomepageBanner::find($id);
        if ($Banner->estatus==1){
            $Banner->estatus = 2;
            $Banner->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($Banner->estatus==2){
            $Banner->estatus = 1;
            $Banner->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }
}
