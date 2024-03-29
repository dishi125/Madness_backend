<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    private $page = "Settings";

    public function index(){
        $Settings = Settings::where('estatus',1)->first();
        $canWrite = false;
        $page_id = ProjectPage::where('route_url','admin.settings.list')->pluck('id')->first();
        if( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
            $canWrite = true;
        }
        return view('admin.settings.list',compact('Settings','canWrite'))->with('page',$this->page);
    }

    public function editUserDiscountPercentage(){
        $Settings = Settings::find(1);
        return response()->json($Settings);
    }

    public function editShippingCost(){
        $Settings = Settings::find(1);
        return response()->json($Settings);
    }

    public function editPremiumUserMembershipFee(){
        $Settings = Settings::find(1);
        return response()->json($Settings);
    }

    public function updateUserDiscountPercentage(Request $request){
        $messages = [
            'user_discount_percentage.required' =>'Please provide a user discount percentage',
        ];

        $validator = Validator::make($request->all(), [
            'user_discount_percentage' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $Settings = Settings::find(1);
        if(!$Settings){
            return response()->json(['status' => '400']);
        }
        $Settings->user_discount_percentage = $request->user_discount_percentage;
        $Settings->save();
        return response()->json(['status' => '200','user_discount_percentage' => $Settings->user_discount_percentage]);
    }

    public function updateShippingCost(Request $request){
        $messages = [
            'shipping_cost.required' =>'Please provide a shipping cost',
        ];

        $validator = Validator::make($request->all(), [
            'shipping_cost' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $Settings = Settings::find(1);
        if(!$Settings){
            return response()->json(['status' => '400']);
        }
        $Settings->shipping_cost = $request->shipping_cost;
        $Settings->save();
        return response()->json(['status' => '200','shipping_cost' => $Settings->shipping_cost]);
    }

    public function updatePremiumUserMembershipFee(Request $request){
        $messages = [
            'premium_user_membership_fee.required' =>'Please provide a premium user membership fee',
        ];

        $validator = Validator::make($request->all(), [
            'premium_user_membership_fee' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $Settings = Settings::find(1);
        if(!$Settings){
            return response()->json(['status' => '400']);
        }
        $Settings->premium_user_membership_fee = $request->premium_user_membership_fee;
        $Settings->save();
        return response()->json(['status' => '200','premium_user_membership_fee' => $Settings->premium_user_membership_fee]);
    }

    public function editMinOrderAmount(){
        $Settings = Settings::find(1);
        return response()->json($Settings);
    }

    public function updateMinOrderAmount(Request $request){
        $messages = [
            'min_order_amount.required' =>'Please provide a minimum order amount',
        ];

        $validator = Validator::make($request->all(), [
            'min_order_amount' => 'required|numeric',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $Settings = Settings::find(1);
        if(!$Settings){
            return response()->json(['status' => '400']);
        }
        $Settings->min_order_amount = $request->min_order_amount;
        $Settings->save();
        return response()->json(['status' => '200','min_order_amount' => $Settings->min_order_amount]);
    }

}
