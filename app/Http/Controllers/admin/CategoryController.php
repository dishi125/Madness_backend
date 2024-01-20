<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    private $page = "Category";

    public function index(){
        $action = "list";
        $categories = Category::where('estatus',1)->get();
        return view('admin.categories.list',compact('action','categories'))->with('page',$this->page);
    }

    public function create(){
        $action = "create";

        $categories = Category::where('estatus',1)->get()->toArray();
        $sr_no = Category::where('estatus',1)->orderBy('sr_no','desc')->pluck('sr_no')->first();
        $attributes = Attribute::where('estatus',1)->where('is_specification',0)->get()->toArray();
        $specifications = Attribute::where('estatus',1)->where('is_specification',1)->get()->toArray();
        return view('admin.categories.list',compact('action','categories','sr_no','attributes','specifications'))->with('page',$this->page);
    }

    public function save(Request $request){
        $messages = [
            'sr_no.required' =>'Please provide valid Serial Number',
            'sr_no.numeric' =>'Please provide valid Serial Number',
            'category_name.required' =>'Please provide a Category Name',
            'catImg.required' =>'Please provide a Category Image',
        ];

        if(isset($request->action) && $request->action=="update"){
            $validator = Validator::make($request->all(), [
                'sr_no' => 'required|numeric',
                'category_name' => 'required',
                'catImg' => 'required',
            ], $messages);
        }
        else{
            $validator = Validator::make($request->all(), [
                'sr_no' => 'required|numeric',
                'category_name' => 'required',
                'catImg' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if (isset($request->action) && $request->action=="update"){
            $action = "update";
            $category = Category::find($request->category_id);

            if(!$category){
                return response()->json(['status' => '400']);
            }

            if ($category->category_thumb != $request->catImg){
                if(isset($category->category_thumb)) {
                    $image = public_path($category->category_thumb);
                    if (file_exists($image)) {
                        unlink($image);
                    }
                }
                $category->category_thumb = $request->catImg;

            }

            $category->sr_no = $request->sr_no;
            $category->category_name = $request->category_name;
            $category->parent_category_id = isset($request->parent_category_id)?$request->parent_category_id:0;
            $category->attribute_id_variation = isset($request->attribute_id_variation) ? $request->attribute_id_variation : null;
            if (isset($request->attribute_id_req_spec) && !empty($request->attribute_id_req_spec) && $category->parent_category_id!=0){
                $attribute_id_req_spec = implode(",",$request->attribute_id_req_spec);
                $category->attribute_id_req_spec = $attribute_id_req_spec;
            }
            else{
                $category->attribute_id_req_spec = null;
            }

            if (isset($request->attribute_id_opt_spec) && !empty($request->attribute_id_opt_spec) && $category->parent_category_id!=0){
                $attribute_id_opt_spec = implode(",",$request->attribute_id_opt_spec);
                $category->attribute_id_opt_spec = $attribute_id_opt_spec;
            }
            else{
                $category->attribute_id_opt_spec = null;
            }
        }
        else{
            $action = "add";
            $category = new Category();
            $category->sr_no = $request->sr_no;
            $category->category_name = $request->category_name;
            $category->parent_category_id = isset($request->parent_category_id)?$request->parent_category_id:0;
            $category->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $category->category_thumb = $request->catImg;
            $category->attribute_id_variation = isset($request->attribute_id_variation) ? $request->attribute_id_variation : null;
            if (isset($request->attribute_id_req_spec) && !empty($request->attribute_id_req_spec) && $category->parent_category_id!=0){
                $attribute_id_req_spec = implode(",",$request->attribute_id_req_spec);
                $category->attribute_id_req_spec = $attribute_id_req_spec;
            }

            if (isset($request->attribute_id_opt_spec) && !empty($request->attribute_id_opt_spec) && $category->parent_category_id!=0){
                $attribute_id_opt_spec = implode(",",$request->attribute_id_opt_spec);
                $category->attribute_id_opt_spec = $attribute_id_opt_spec;
            }
        }

        $category->order_return_days = isset($request->order_return_days) ? $request->order_return_days : 0;
        $category->save();

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allcategorylist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 =>'sr_no',
                1 =>'category_thumb',
                2 => 'category_name',
                3 => 'parent_category_name',
                4 => 'total_products',
                5 => 'estatus',
                6 => 'created_at',
                7 => 'action',
            );
            $totalData = Category::count();
            if (isset($request->category_id_filter) && $request->category_id_filter!=''){
                $totalData = Category::where('parent_category_id',$request->category_id_filter)->count();
            }
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
                $categories = Category::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
                if (isset($request->category_id_filter) && $request->category_id_filter!=''){
                    $categories = Category::where('parent_category_id',$request->category_id_filter)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order,$dir)
                            ->get();
                }
            }
            else {
                $search = $request->input('search.value');
                $categories =  Category::where('sr_no','LIKE',"%{$search}%")
                    ->orWhere('category_name', 'LIKE',"%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
                if (isset($request->category_id_filter) && $request->category_id_filter!=''){
                    $categories = Category::where('parent_category_id',$request->category_id_filter)
                        ->where(function($query) use ($search){
                            $query->where('sr_no','LIKE',"%{$search}%")
                                ->orWhere('category_name', 'LIKE',"%{$search}%");
                        })
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
                }

                $totalFiltered = Category::where('sr_no','LIKE',"%{$search}%")
                    ->orWhere('category_name', 'LIKE',"%{$search}%")
                    ->count();
                if (isset($request->category_id_filter) && $request->category_id_filter!=''){
                    $totalFiltered = Category::where('parent_category_id',$request->category_id_filter)
                        ->where(function($query) use ($search){
                            $query->where('sr_no','LIKE',"%{$search}%")
                                ->orWhere('category_name', 'LIKE',"%{$search}%");
                        })
                        ->count();
                }
            }

            $data = array();

            if(!empty($categories))
            {
//                $i=1;
                foreach ($categories as $category)
                {
                    $page_id = ProjectPage::where('route_url','admin.categories.list')->pluck('id')->first();

                    if( $category->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="CategoryStatuscheck_'. $category->id .'" onchange="chageCategoryStatus('. $category->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($category->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="CategoryStatuscheck_'. $category->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $category->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="CategoryStatuscheck_'. $category->id .'" onchange="chageCategoryStatus('. $category->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($category->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="CategoryStatuscheck_'. $category->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    if(isset($category->category_thumb) && $category->category_thumb!=null){
                        $thumb_path = url('public/'.$category->category_thumb);
                    }

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editCategoryBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$category->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteCategoryBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteCategoryModal" onclick="" data-id="' .$category->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }
//                    $nestedData['sr_no'] = $category->sr_no;
                    $nestedData['category_thumb'] = '<img src="'. $thumb_path .'" width="50px" height="50px" alt="Thumbnail">';
                    $nestedData['category_name'] = $category->category_name;
                    $nestedData['parent_category_name'] = $category->parent_category_id!=0 ? CategoryName($category->parent_category_id) : '-';
                    $nestedData['total_products'] = $category->total_products;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($category->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;
//                    $i=$i+1;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );

//            return json_encode($json_data);
            echo json_encode($json_data);
        }
    }

    public function changecategorystatus($id){
        $category = Category::find($id);
        if ($category->estatus==1){
            $category->estatus = 2;
            $category->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($category->estatus==2){
            $category->estatus = 1;
            $category->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function deletecategory($id){
        $category = Category::find($id);
        if ($category){
            $image = $category->category_thumb;
            $category->estatus = 3;
            $category->save();

            $category->delete();
            $image = public_path($image);
            if (file_exists($image)) {
                unlink($image);
            }
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function editcategory($id){
        $action = "edit";

        $categories = Category::where('estatus',1)->where('id',"!=",$id)->where('parent_category_id',"!=",$id)->get()->toArray();
        $category = Category::find($id);

        $attributes = Attribute::where('estatus',1)->where('is_specification',0)->get()->toArray();
        $specifications = Attribute::where('estatus',1)->where('is_specification',1)->get()->toArray();

        $parent_category_data = Category::where('id',$category->parent_category_id)->pluck('parent_category_id')->first();
        $is_sub_child = false;
        if ($parent_category_data != 0){
            $is_sub_child = true;
        }
        return view('admin.categories.list',compact('action','categories','category','attributes','specifications','is_sub_child'))->with('page',$this->page);
    }

    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadCatIcon'){
            if ($request->hasFile('files')) {
                $image = $request->file('files')[0];
//                dd($image);
                $image_name = 'categoryThumb_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/categoryThumb');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/categoryThumb/'.$image_name]);
//                $category->category_thumb = $image_name;
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeCatIcon'){
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

    public function checkparentcat($id){
        $category = Category::where('id',$id)->first();

        if ($category->parent_category_id == 0){
            return true; //main parent category
        }
        else{
            return false; //child or sub-child category
        }
    }

}
