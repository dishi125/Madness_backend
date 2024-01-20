<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CustomerDeviceToken;
use App\Models\Notification;
use App\Models\PremiumUserTransaction;
use App\Models\ProductVariant;
use App\Models\Suggestion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    public function verify_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required',
            'otp' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('otp',$request->otp)->where('estatus',1)->first();

        if ( $user && isset($user['otp_created_at']) ){
            $t1 = Carbon::parse(now());
            $t2 = Carbon::parse($user['otp_created_at']);
            $diff = $t1->diff($t2);
//            dd(Carbon::now()->toDateTimeString(),$user['otp_created_at'],$diff->i);
            $user->otp = null;
            $user->otp_created_at = null;
            $user->save();

            if($diff->i > 30) {
                return $this->sendError('OTP verification Failed.', "verification Failed", []);
            }

            $data['token'] =  $user->createToken('MyApp')-> accessToken;
            $data['profile_data'] =  new UserResource($user);
            $final_data = array();
            array_push($final_data,$data);
            return $this->sendResponseWithData($final_data,'OTP verified successfully.');
        }
        else{
            return $this->sendError('OTP verification Failed.', "verification Failed", []);
        }
    }

    public function send_otp(Request $request){
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('role',3)->where('estatus',1)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $data = array();
        $otp['otp'] =  mt_rand(1000,9999);
        send_sms($request->mobile_no, $otp['otp']);

        array_push($data,$otp);
//        $user->otp = $data['otp'];
//        $user->otp_created_at = Carbon::now();
//        $user->save();
        return $this->sendResponseWithData($data, "User OTP generated.");
    }

    public function edit_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::find($request->user_id);
        if (!$user)
        {
            return $this->sendError('User Not Exist.', "Not Found Error", []);
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->full_name = $request->first_name." ".$request->last_name;
        $user->dob = $request->dob;
        if (isset($request->gender)) {
            $user->gender = $request->gender;
        }
        $user->email = isset($request->email) ? $request->email : null;

        if ($request->hasFile('profile_pic')) {
            if(isset($user->profile_pic)) {
                $old_image = public_path('images/profile_pic/' . $user->profile_pic);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }

            $image = $request->file('profile_pic');
            $ext = $image->getClientOriginalExtension();
            $ext = strtolower($ext);
            // $all_ext = array("png","jpg", "jpeg", "jpe", "jif", "jfif", "jfi","tiff","tif","raw","arw","svg","svgz","bmp", "dib","mpg","mp2","mpeg","mpe");
            $all_ext = array("png", "jpg", "jpeg");
            if (!in_array($ext, $all_ext)) {
                return $this->sendError('Invalid type of image.', "Extension error", []);
            }

            $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/profile_pic');
            $image->move($destinationPath, $image_name);
            $user->profile_pic = $image_name;
        }
        $user->save();

        return $this->sendResponseWithData(new UserResource($user),'User profile updated successfully.');
    }

    public function submit_refcode(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'referral_code' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->first();
        $user_referral_code = User::where('referral_id',$request->referral_code)->where('estatus',1)->first();

        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        if (!$user_referral_code){
            return $this->sendError("Invalid Referral Code", "Not Found Error", []);
        }

        $user->parent_user_id = $user_referral_code->id;
        $user->save();
        return $this->sendResponseSuccess("Referral Code Submitted Successfully");
    }

    public function view_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('role',3)->where('estatus',1)->first();

        if (!$user){
            return $this->sendError("You can not view this profile", "Invalid user", []);
        }

        $data = array();
        array_push($data,new UserResource($user));
        return $this->sendResponseWithData($data,'User profile Retrieved successfully.');
    }

    public function collections(){
        $collections = Collection::with('applicationdropdown')->where('estatus',1)->orderBy('sr_no','ASC')->get();
        $collections_arr = array();
        foreach ($collections as $collection){
            $temp = array();
            $temp['id'] = $collection->id;
            $temp['title'] = $collection->title;
            $temp['image'] = 'public/'.$collection->image;
            $temp['application_dropdown'] = $collection->applicationdropdown->title;

            if($collection->application_dropdown_id == 5){
                $category = Category::where('id',$collection->value)->first();
                $product = ProductVariant::where('id',$collection->product_variant_id)->pluck('product_title')->first();
                $temp['value_id'] = $collection->product_variant_id;
                $temp['value_title'] = $product;
            }
            elseif($collection->application_dropdown_id == 7){
                $category = Category::where('id',$collection->value)->first();
                $temp['value_id'] = $category->id;
                $temp['value_title'] = $category->category_name;
            }
            else{
                $temp['value_id'] = null;
                $temp['value_title'] = $collection->value;
            }

            array_push($collections_arr,$temp);
        }

        return $this->sendResponseWithData($collections_arr,"Collections Retrieved Successfully.");
    }

    public function register_user(Request $request){
        $messages = [
            'profile_pic.image' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'profile_pic.mimes' =>'Please provide a Valid Extension Image(e.g: .jpg .png)',
            'first_name.required' =>'Please provide a First Name',
            'last_name.required' =>'Please provide a Last Name',
            'mobile_no.required' =>'Please provide a Mobile No.',
            'dob.required' =>'Please provide a Date of Birth.',
            'email.required' =>'Please provide a e-mail address.',
            'password.required' =>'Please provide a password.',
            'gender.required' =>'Please provide a gender.',
        ];

        $validator = Validator::make($request->all(), [
            'profile_pic' => 'image|mimes:jpeg,png,jpg',
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_no' => 'required|numeric|digits:10',
            'dob' => 'required',
            'email' => 'required',
            'password' => 'required',
            'gender' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->full_name = $request->first_name." ".$request->last_name;
        $user->mobile_no = $request->mobile_no;
        $user->gender = $request->gender;
        $user->dob = $request->dob;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->decrypted_password = $request->password;
        $user->role = 3;
        $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));

        if ($request->hasFile('profile_pic')) {
            $image = $request->file('profile_pic');
            $image_name = 'profilePic_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/profile_pic');
            $image->move($destinationPath, $image_name);
            $user->profile_pic = $image_name;
        }

        $user->save();
        return $this->sendResponseSuccess("User Registered Successfully");
    }

    public function update_membership(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'amount' => 'required',
            'transaction_id' => 'required',
            'payment_mode' => 'required',
            'transaction_date' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $user->is_premium = 1;
        $password_string = '!@#$%*&abcdefghijklmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789';
        $password = substr(str_shuffle($password_string), 0, 12);
        $user->decrypted_password = $password;
        $user->password = Hash::make($password);
        $user->save();

        $premium_user_transaction = new PremiumUserTransaction();
        $premium_user_transaction->user_id = $request->user_id;
        $premium_user_transaction->amount = $request->amount;
        $premium_user_transaction->transaction_id = $request->transaction_id;
        $premium_user_transaction->payment_mode = $request->payment_mode;
        $premium_user_transaction->transaction_date = $request->transaction_date;
        $premium_user_transaction->save();

        $data = array();
        $temp['user_panel_url'] = "https://madnessuserpanel.matoresell.com";
        $temp['email'] = $user->email;
        $temp['password'] = $user->decrypted_password;
        array_push($data,$temp);
        return $this->sendResponseWithData($data,"User Membership updated.");
    }

    public function update_token(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'token' => 'required',
            'device_type' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $device = CustomerDeviceToken::where('user_id',$request->user_id)->first();
        if ($device){
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        else{
            $device = new CustomerDeviceToken();
            $device->user_id = $request->user_id;
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        $device->save();

        return $this->sendResponseSuccess("Device Token updated.");
    }

    public function notifications(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        $notifications = Notification::with('applicationdropdown')->whereIn('user_id',[0,$request->user_id])->orderBy('created_at','DESC')->get();
        $notifications_arr = array();
        foreach ($notifications as $notification){
            $temp = array();
            $temp['id'] = $notification->id;
            $temp['title'] = $notification->notify_title;
            $temp['desc'] = $notification->notify_desc;
            $temp['image'] = isset($notification->notify_thumb)?'public/'.$notification->notify_thumb:null;
            $temp['application_dropdown'] = isset($notification->application_dropdown_id)?$notification->applicationdropdown->title:null;

            if($notification->application_dropdown_id == 5){
                $category = Category::where('id',$notification->parent_value)->first();
                $product = ProductVariant::where('id',$notification->value)->pluck('product_title')->first();
                $temp['value_id'] = $notification->value;
                $temp['value_title'] = $product;
            }
            elseif($notification->application_dropdown_id == 7){
                $category = Category::where('id',$notification->value)->first();
                $temp['value_id'] = $category->id;
                $temp['value_title'] = $category->category_name;
            }
            else{
                $temp['value_id'] = null;
                $temp['value_title'] = $notification->value;
            }

            $temp['type'] = $notification->type;
            array_push($notifications_arr,$temp);
        }

        return $this->sendResponseWithData($notifications_arr,"Notifications Retrieved Successfully.");
    }

    public function give_suggestion(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'message' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $suggestion = new Suggestion();
        $suggestion->user_id = $request->user_id;
        $suggestion->message = $request->message;
        $suggestion->save();

        return $this->sendResponseSuccess("Suggestion Submitted Successfully.");
    }
}
