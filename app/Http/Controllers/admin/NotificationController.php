<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationDropdown;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CustomerDeviceToken;
use App\Models\Notification;
use App\Models\ProductVariant;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    private $page = "Notifications";

    public function index(){
        $action = "list";
        return view('admin.notifications.list',compact('action'))->with('page',$this->page);
    }

    public function create(){
        $action = "create";
        $application_dropdowns = ApplicationDropdown::get();
        return view('admin.notifications.list',compact('action','application_dropdowns'))->with('page',$this->page);
    }

    public function getNotificationInfoVal(Request $request){
        $data = getDropdownInfoVal($request->NotificationInfo);
        return ["html" => $data['html'], 'categories' => $data['categories']];
    }

    public function getproducts($cat_id){
        $variants_arr = getproducts($cat_id);
        return $variants_arr;
    }

    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadNotificationImg'){
            if ($request->hasFile('files')) {
                $image = $request->file('files')[0];
                $image_name = 'Notification_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/Notification');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/Notification/'.$image_name]);
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeNotificationImg'){
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

    public function save(Request $request){
        $messages = [
            'NotificationImg.required' =>'Please provide a Notification Image',
            'notify_title.required' =>'Please provide a Notification Title',
            'notify_desc.required' =>'Please provide a Notification Description',
        ];

        $validator = Validator::make($request->all(), [
            'NotificationImg' => 'required',
            'notify_title' => 'required',
            'notify_desc' => 'required',
        ], $messages);

        if($request->NotificationInfo == 3 || $request->NotificationInfo == 5 || $request->NotificationInfo == 7 || $request->NotificationInfo == 10 || $request->NotificationInfo == 14){
            $messages = [
                'NotificationImg.required' =>'Please provide a Notification Image',
                'notify_title.required' =>'Please provide a Notification Title',
                'notify_desc.required' =>'Please provide a Notification Description',
            ];

            $validator = Validator::make($request->all(), [
                'NotificationImg' => 'required',
                'notify_title' => 'required',
                'notify_desc' => 'required',
                'value' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if (isset($request->action) && $request->action=="update"){
            $action = "update";
            $Notification = Notification::find($request->notification_id);

            if(!$Notification){
                return response()->json(['status' => '400']);
            }

            if ($Notification->notify_thumb != $request->NotificationImg){
                if(isset($Notification->notify_thumb)) {
                    $image = public_path($Notification->notify_thumb);
                    if (file_exists($image)) {
                        unlink($image);
                    }
                }
                $Notification->notify_thumb = $request->NotificationImg;
            }

            $Notification->notify_title = $request->notify_title;
            $Notification->notify_desc = $request->notify_desc;
            $Notification->application_dropdown_id = $request->NotificationInfo;
            if ($Notification->application_dropdown_id == 5){
                $Notification->value = isset($request->product) ? $request->product : null;
                $Notification->parent_value = $request->value;
            }
            else{
                $Notification->value = $request->value;
                $Notification->parent_value = null;
            }
        }
        else{
            $action = "add";
            $Notification = new Notification();
            $Notification->user_id = 0;
            $Notification->notify_title = $request->notify_title;
            $Notification->notify_desc = $request->notify_desc;
            $Notification->notify_thumb = $request->NotificationImg;
            $Notification->application_dropdown_id = $request->NotificationInfo;
            if ($Notification->application_dropdown_id == 5){
                $Notification->value = isset($request->product) ? $request->product : null;
                $Notification->parent_value = $request->value;
            }
            else{
                $Notification->value = $request->value;
            }
            $Notification->type = "custom";
        }

        $Notification->save();

        //send notification to customers
        if ($action == "add"){
            $notification_array['title'] = $Notification->notify_title;
            $notification_array['message'] = $Notification->notify_desc;
            $notification_array['image'] = public_path($Notification->notify_thumb);
            sendPushNotification_customers($notification_array);
        }

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allnotificationlist(Request $request){
        if ($request->ajax()) {
            $columns = array(
                0 => 'id',
                1 => 'notify_thumb',
                2 => 'notify_title',
                3 => 'notify_desc',
                4 => 'application_dropdown_id',
                5 => 'value',
                6 => 'created_at',
                7 => 'action',
            );
            $totalData = Notification::where('type','custom')->count();
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
                $Notifications = Notification::with('applicationdropdown')
                    ->where('type','custom')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $Notifications =  Notification::with('applicationdropdown')
                    ->where('type','custom')
                    ->where(function($query) use($search){
                        $query->where('notify_title','LIKE',"%{$search}%")
                            ->orWhere('notify_desc','LIKE',"%{$search}%")
                            ->orWhereHas('applicationdropdown',function ($mainQuery) use($search) {
                                $mainQuery->where('title', 'Like', '%' . $search . '%');
                            })
                            ->orWhere('value','LIKE',"%{$search}%");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($Notifications->toArray());
            }

            $data = array();

            if(!empty($Notifications))
            {
                foreach ($Notifications as $Notification)
                {
                    $page_id = ProjectPage::where('route_url','admin.notifications.list')->pluck('id')->first();

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="sendNotificationBtn" class="btn btn-gray text-warning btn-sm" data-id="' .$Notification->id. '"><i class="fa fa-bell-o" aria-hidden="true"></i></button>';
                        $action .= '<button id="editNotificationBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$Notification->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteNotificationBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteNotificationModal" onclick="" data-id="' .$Notification->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $value = $Notification->value;
                    if ($Notification->application_dropdown_id == 7){
                        $category = Category::where('id',$Notification->value)->first();
                        $value = $category->category_name;
                    }
                    if ($Notification->application_dropdown_id == 5){
                        $ProductVariant = ProductVariant::where('id',$Notification->value)->first();
                        $value = isset($ProductVariant)?$ProductVariant->product_title:'';
                    }

                    $img_path = url('public/'.$Notification->notify_thumb);
                    $nestedData['notify_thumb'] = '<img src="'. $img_path .'" width="50px" height="50px" alt="Notification Image">';
                    $nestedData['notify_title'] = $Notification->notify_title;
                    $nestedData['notify_desc'] = $Notification->notify_desc;
                    $nestedData['application_dropdown_id'] = $Notification->applicationdropdown->title;
                    $nestedData['value'] = $value;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($Notification->created_at));
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

    public function editnotification($id){
        $action = "edit";
        $notification = Notification::find($id);
        $application_dropdowns = ApplicationDropdown::get();
        $categories = Category::where('estatus',1)->orderBy('created_at','DESC')->get();

        $products = "";
        if($notification->application_dropdown_id == 5) {
            $products = getproducts($notification->parent_value);
        }

        return view('admin.notifications.list',compact('action','notification','application_dropdowns','categories','products'))->with('page',$this->page);
    }

    public function deletenotification($id){
        $notification = Notification::find($id);
        if ($notification){
            $image = $notification->notify_thumb;
            $notification->estatus = 3;
            $notification->save();

            $notification->delete();
            $image = public_path($image);
            if (file_exists($image)) {
                unlink($image);
            }
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function sendnotification($id){
        $Notification = Notification::where('id',$id)->first();

        if (!$Notification){
            return ['status' => 400];
        }

        $notification_array['title'] = $Notification->notify_title;
        $notification_array['message'] = $Notification->notify_desc;
        $notification_array['image'] = public_path($Notification->notify_thumb);
        sendPushNotification_customers($notification_array);
        return ['status' => 200];
    }
}
