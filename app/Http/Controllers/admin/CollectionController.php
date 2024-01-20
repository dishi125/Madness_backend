<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDropdown;
use App\Models\Category;
use App\Models\Collection;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    private $page = "Collections";

    public function index(){
        $action = "list";
        return view('admin.collections.list',compact('action'))->with('page',$this->page);
    }

    public function create(){
        $action = "create";
        $sr_no = Collection::where('estatus',1)->orderBy('sr_no','desc')->pluck('sr_no')->first();
        $application_dropdowns = ApplicationDropdown::get();
        return view('admin.collections.list',compact('action','sr_no','application_dropdowns'))->with('page',$this->page);
    }

    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadCollectionImg'){
            if ($request->hasFile('files')) {
                $image = $request->file('files')[0];
                $image_name = 'Collection_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/Collection');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/Collection/'.$image_name]);
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeCollectionImg'){
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

    public function getCollectionInfoVal(Request $request){
        $data = getDropdownInfoVal($request->CollectionInfo);
        return ["html" => $data['html'], 'categories' => $data['categories']];
    }

    public function getproducts($cat_id){
        $variants_arr = getproducts($cat_id);
        return $variants_arr;
    }

    public function save(Request $request){
        $messages = [
            'sr_no.required' =>'Please provide valid Serial Number',
            'sr_no.numeric' =>'Please provide valid Serial Number',
            'CollectionImg.required' =>'Please provide a Collection Image',
            'title.required' =>'Please provide a Collection Title',
        ];

        $validator = Validator::make($request->all(), [
            'sr_no' => 'required|numeric',
            'CollectionImg' => 'required',
            'title' => 'required',
        ], $messages);

        if($request->CollectionInfo == 3 || $request->CollectionInfo == 5 || $request->CollectionInfo == 7 || $request->CollectionInfo == 10 || $request->CollectionInfo == 14){
            $messages = [
                'sr_no.required' =>'Please provide valid Serial Number',
                'sr_no.numeric' =>'Please provide valid Serial Number',
                'CollectionImg.required' =>'Please provide a Collection Image',
                'title.required' =>'Please provide a Collection Title',
            ];

            $validator = Validator::make($request->all(), [
                'sr_no' => 'required|numeric',
                'CollectionImg' => 'required',
                'title' => 'required',
                'value' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if (isset($request->action) && $request->action=="update"){
            $action = "update";
            $Collection = Collection::find($request->collection_id);

            if(!$Collection){
                return response()->json(['status' => '400']);
            }

            if ($Collection->image != $request->CollectionImg){
                if(isset($Collection->image)) {
                    $image = public_path($Collection->image);
                    if (file_exists($image)) {
                        unlink($image);
                    }
                }
                $Collection->image = $request->CollectionImg;
            }

            $Collection->sr_no = $request->sr_no;
            $Collection->title = $request->title;
            $Collection->application_dropdown_id = $request->CollectionInfo;
            $Collection->value = $request->value;
            $Collection->product_variant_id = isset($request->product) ? $request->product : null;
        }
        else{
            $action = "add";
            $Collection = new Collection();
            $Collection->sr_no = $request->sr_no;
            $Collection->title = $request->title;
            $Collection->image = $request->CollectionImg;
            $Collection->application_dropdown_id = $request->CollectionInfo;
            $Collection->value = $request->value;
            $Collection->product_variant_id = isset($request->product) ? $request->product : null;
        }

        $Collection->save();

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allcollectionlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'sr_no',
                1 => 'image',
                2 => 'title',
                3 => 'application_dropdown_id',
                4 => 'value',
                5 => 'estatus',
                6 => 'created_at',
                7 => 'action',
            );
            $totalData = Collection::count();
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
                $Collections = Collection::with('applicationdropdown')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Collections =  Collection::with('applicationdropdown')
                    ->where('sr_no','LIKE',"%{$search}%")
                    ->orWhere('title','LIKE',"%{$search}%")
                    ->orWhereHas('applicationdropdown',function ($mainQuery) use($search) {
                        $mainQuery->where('title', 'Like', '%' . $search . '%');
                    })
                    ->orWhere('value','LIKE',"%{$search}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($Collections->toArray());
            }

            $data = array();

            if(!empty($Collections))
            {
                foreach ($Collections as $Collection)
                {
                    $page_id = ProjectPage::where('route_url','admin.collections.list')->pluck('id')->first();

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editCollectionBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$Collection->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteCollectionBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteCollectionModal" onclick="" data-id="' .$Collection->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $value = $Collection->value;
                    if ($Collection->application_dropdown_id == 5 || $Collection->application_dropdown_id == 7){
                        $category = Category::where('id',$Collection->value)->first();
                        $value = $category->category_name;
                    }

                    if( $Collection->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="Collectionstatuscheck_'. $Collection->id .'" onchange="changeCollectionStatus('. $Collection->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($Collection->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="Collectionstatuscheck_'. $Collection->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $Collection->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="Collectionstatuscheck_'. $Collection->id .'" onchange="changeCollectionStatus('. $Collection->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($Collection->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="Collectionstatuscheck_'. $Collection->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    $img_path = url('public/'.$Collection->image);
                    $nestedData['image'] = '<img src="'. $img_path .'" width="50px" height="50px" alt="Banner Image">';
                    $nestedData['title'] = $Collection->title;
                    $nestedData['application_dropdown_id'] = $Collection->applicationdropdown->title;
                    $nestedData['value'] = $value;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($Collection->created_at));
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

    public function changeCollectionStatus($id){
        $Collection = Collection::find($id);
        if ($Collection->estatus==1){
            $Collection->estatus = 2;
            $Collection->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($Collection->estatus==2){
            $Collection->estatus = 1;
            $Collection->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editcollection($id){
        $action = "edit";
        $collection = Collection::find($id);
        $application_dropdowns = ApplicationDropdown::get();
        $categories = Category::where('estatus',1)->orderBy('created_at','DESC')->get();

        $products = "";
        if($collection->application_dropdown_id == 5) {
            $products = getproducts($collection->value);
        }

        return view('admin.collections.list',compact('action','collection','application_dropdowns','categories','products'))->with('page',$this->page);
    }

    public function deletecollection($id){
        $collection = Collection::find($id);
        if ($collection){
            $image = $collection->image;
            $collection->estatus = 3;
            $collection->save();

            $collection->delete();
            $image = public_path($image);
            if (file_exists($image)) {
                unlink($image);
            }
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

}
