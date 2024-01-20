<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EndUserController extends Controller
{
    private $page = "Users";

    public function index(){
        return view('admin.end_users.list')->with('page',$this->page);
    }

    public function allEnduserlist(Request $request){
        if ($request->ajax()) {
            $tab_type = $request->tab_type;
            if ($tab_type == "active_end_user_tab"){
                $estatus = 1;
            }
            elseif ($tab_type == "deactive_end_user_tab"){
                $estatus = 2;
            }

            $columns = array(
                0 =>'id',
                1 =>'profile_pic',
                2=> 'contact_info',
                3=> 'is_premium',
                4=> 'estatus',
                5=> 'created_at',
                6=> 'action',
            );

            $totalData = User::where('role',3);
            if (isset($estatus)){
                $totalData = $totalData->where('estatus',$estatus);
            }
            $totalData = $totalData->count();

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
//            dd($columns[$request->input('order.0.column')]);
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $users = User::where('role',3);
                if (isset($estatus)){
                    $users = $users->where('estatus',$estatus);
                }
                $users = $users->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $users =  User::where('role',3);
                if (isset($estatus)){
                    $users = $users->where('estatus',$estatus);
                }
                $users = $users->where(function($query) use($search){
                    $query->where('first_name','LIKE',"%{$search}%")
                        ->orWhere('last_name','LIKE',"%{$search}%")
                        ->orWhere('email', 'LIKE',"%{$search}%")
                        ->orWhere('mobile_no', 'LIKE',"%{$search}%")
                        ->orWhere('created_at', 'LIKE',"%{$search}%");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($users->toArray());
            }

            $data = array();

            if(!empty($users))
            {
                foreach ($users as $user)
                {
                    $page_id = ProjectPage::where('route_url','admin.end_users.list')->pluck('id')->first();

                    if( $user->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" onchange="changeEndUserStatus('. $user->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($user->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $user->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" onchange="changeEndUserStatus('. $user->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($user->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    if(isset($user->profile_pic) && $user->profile_pic!=null){
                        $profile_pic = url('public/images/profile_pic/'.$user->profile_pic);
                    }
                    else{
                        $profile_pic = url('public/images/default_avatar.jpg');
                    }

                    $contact_info = '';
                    if (isset($user->email)){
                        $contact_info .= '<span><i class="fa fa-envelope" aria-hidden="true"></i> ' .$user->email .'</span>';
                    }
                    if (isset($user->mobile_no)){
                        $contact_info .= '<span><i class="fa fa-phone" aria-hidden="true"></i> ' .$user->mobile_no .'</span>';
                    }

                    if(isset($user->full_name)){
                        $full_name = $user->full_name;
                    }
                    else{
                        $full_name="";
                    }

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editEndUserBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#EndUserModal" onclick="" data-id="' .$user->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteEndUserBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteEndUserModal" onclick="" data-id="' .$user->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    if ($user->is_premium == 0){
                        $is_premium = '<span class="label label-primary">Free</span>';
                    }
                    elseif ($user->is_premium == 1){
                        $is_premium = '<span class="label label-warning">Premium</span>';
                    }

                    $nestedData['profile_pic'] = '<img src="'. $profile_pic .'" width="50px" height="50px" alt="Profile Pic"><span>'.$full_name.'</span>';
                    $nestedData['contact_info'] = $contact_info;
                    $nestedData['is_premium'] = $is_premium;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($user->created_at));
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

//            return json_encode($json_data);
            echo json_encode($json_data);
        }
    }

    public function addorupdateEnduser(Request $request){
        $messages = [
            'profile_pic.image' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'profile_pic.mimes' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'first_name.required' =>'Please provide a First Name',
            'last_name.required' =>'Please provide a Last Name',
            'mobile_no.required' =>'Please provide a Mobile No.',
            'dob.required' =>'Please provide a Date of Birth.',
            'email.required' =>'Please provide a valid E-mail address.',
            'password.required' =>'Please provide a Password.',
        ];

        if ($request->is_premium == 1){
            $validator = Validator::make($request->all(), [
                'profile_pic' => 'image|mimes:jpeg,png,jpg',
                'first_name' => 'required',
                'last_name' => 'required',
                'mobile_no' => 'required|numeric|digits:10',
                'dob' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ], $messages);
        }
        else{
            $validator = Validator::make($request->all(), [
                'profile_pic' => 'image|mimes:jpeg,png,jpg',
                'first_name' => 'required',
                'last_name' => 'required',
                'mobile_no' => 'required|numeric|digits:10',
                'dob' => 'required',
            ], $messages);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $user = User::find($request->user_id);

            if(!$user){
                return response()->json(['status' => '400']);
            }

            $old_image = $user->profile_pic;
            $image_name = $old_image;

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $request->first_name." ".$request->last_name;
            $user->mobile_no = $request->mobile_no;
            $user->gender = $request->gender;
            $user->dob = $request->dob;
            $user->is_premium = isset($request->is_premium)?$request->is_premium:0;
            if ($user->is_premium == 1){
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->decrypted_password = $request->password;
            }
        }
        else{
            $action = "add";
            $user = new User();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->full_name = $request->first_name." ".$request->last_name;
            $user->mobile_no = $request->mobile_no;
            $user->gender = $request->gender;
            $user->dob = $request->dob;
            $user->role = 3;
            $user->is_premium = isset($request->is_premium)?$request->is_premium:0;
            if ($user->is_premium == 1){
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->decrypted_password = $request->password;
            }
            $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $image_name=null;
        }

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/profile_pic');
            $image->move($destinationPath, $image_name);
            if(isset($old_image)) {
                $old_image = public_path('images/profile_pic/' . $old_image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $user->profile_pic = $image_name;
        }

        $user->save();

        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function editEnduser($id){
        $user = User::find($id);
        return response()->json($user);
    }

    public function deleteEnduser($id){
        $user = User::find($id);
        if ($user){
            $user->estatus = 3;
            $user->save();

            $user->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changeEnduserstatus($id){
        $user = User::find($id);
        if ($user->estatus==1){
            $user->estatus = 2;
            $user->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($user->estatus==2){
            $user->estatus = 1;
            $user->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }


}
