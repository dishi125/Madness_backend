<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\User;
use App\Models\UserPermission;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends BaseController
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $mobile_no = $request->mobile_no;
        $user = User::where('mobile_no',$mobile_no)->where('role',3)->first();
        if ($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }
            $data['otp'] =  mt_rand(100000,999999);
            $user->otp = $data['otp'];
            $user->otp_created_at = Carbon::now();
            $user->save();
            $data['user_status'] = 'exist_user';
            $final_data = array();
            array_push($final_data,$data);

            send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User login successfully.');
        }else{
            $data['otp'] =  mt_rand(100000,999999);
            $user = User::create(['mobile_no'=>$mobile_no,'role'=>3,'otp'=>$data['otp'],'otp_created_at'=>Carbon::now(),'referral_id'=>Str::random(5)]);

            $project_page_ids1 = ProjectPage::where('parent_menu',0)->where('is_display_in_menu',0)->pluck('id')->toArray();
            $project_page_ids2 = ProjectPage::where('parent_menu',"!=",0)->where('is_display_in_menu',1)->pluck('id')->toArray();
            $project_page_ids = array_merge($project_page_ids1,$project_page_ids2);
            foreach ($project_page_ids as $pid) {
                $user_permission = new UserPermission();
                $user_permission->user_id = $user->id;
                $user_permission->project_page_id = $pid;
                $user_permission->save();
            }

            $data['user_status'] = 'new_user';
            $final_data = array();
            array_push($final_data,$data);

            send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User registered successfully.');
        }
    }

    public function send_sms(){
        $url = 'https://www.smsgatewayhub.com/api/mt/SendSMS?APIKey=H26o0GZiiEaUyyy0kvOV5g&senderid=MADMRT&channel=2&DCS=0&flashsms=0&number=917622027040&text=Welcome%20to%20Madness%20Mart,%20Your%20One%20time%20verification%20code%20is%205256.%20Regards%20-%20MADNESS%20MART&route=31&EntityId=1301164983812180724&dlttemplateid=1307165088121527950';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        echo $response;
    }
}
