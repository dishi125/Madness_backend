<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends BaseController
{
    public function update_address(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'address_title' => 'required',
            'full_name' => 'required',
            'mobile_no' => 'required|numeric',
            'pincode' => 'required',
            'address' => 'required',
            'landmark' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        if ($request->is_default == 1)
        {
            $CustomerAddresses = CustomerAddress::where('user_id',$request->user_id)->where('estatus',1)->get();
            foreach ($CustomerAddresses as $CustomerAddress){
                $CustomerAddress->is_default = 0;
                $CustomerAddress->save();
            }
        }

        if (isset($request->address_id) && $request->address_id!=0){
            //Edit
            $CustomerAddress = CustomerAddress::find($request->address_id);
        }
        else{
            //Add
            $CustomerAddress = new CustomerAddress();
        }
        $CustomerAddress->user_id = $request->user_id;
        $CustomerAddress->is_default = $request->is_default;
        $CustomerAddress->address_title = $request->address_title;
        $CustomerAddress->full_name = $request->full_name;
        $CustomerAddress->mobile_no = $request->mobile_no;
        $CustomerAddress->pincode = isset($request->pincode) ? $request->pincode : null;
        $CustomerAddress->address = $request->address;
        $CustomerAddress->address2 = isset($request->address2) ? $request->address2 : null;
        $CustomerAddress->landmark = isset($request->landmark) ? $request->landmark : null;
        $CustomerAddress->city = isset($request->city) ? $request->city : null;
        $CustomerAddress->state = isset($request->state) ? $request->state : null;
        $CustomerAddress->country = isset($request->country) ? $request->country : null;
        $CustomerAddress->save();

        return $this->sendResponseWithData(['address_id' => $CustomerAddress->id],"Customer Address Updated Successfully.");
    }

    public function remove_address(Request $request){
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $CustomerAddress = CustomerAddress::find($request->address_id);
        $CustomerAddress->estatus = 3;
        $CustomerAddress->save();
        $CustomerAddress->delete();

        return $this->sendResponseSuccess("Customer Address Removed Successfully.");
    }

    public function address_list(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $CustomerAddresses = CustomerAddress::where('user_id',$request->user_id)->where('estatus',1)->get();
        $CustomerAddresses_arr = array();
        foreach ($CustomerAddresses as $customerAddress){
            $temp = array();
            $temp['address_id'] = $customerAddress->id;
            $temp['is_default'] = $customerAddress->is_default;
            $temp['address_title'] = $customerAddress->address_title;
            $temp['full_name'] = $customerAddress->full_name;
            $temp['mobile_no'] = $customerAddress->mobile_no;
            $temp['pincode'] = $customerAddress->pincode;
            $temp['address'] = $customerAddress->address;
            $temp['address2'] = $customerAddress->address2;
            $temp['landmark'] = $customerAddress->landmark;
            $temp['city'] = $customerAddress->city;
            $temp['state'] = $customerAddress->state;
            $temp['country'] = $customerAddress->country;
            array_push($CustomerAddresses_arr, $temp);
        }

        return $this->sendResponseWithData($CustomerAddresses_arr,"Customer Address List Retrieved Successfully.");
    }
}
