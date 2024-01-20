<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseController
{
    public function update_cart(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'product_variant_id' => 'required',
            'product_id' => 'required',
            'action_type' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $cart = Cart::where('user_id',$request->user_id)->where('product_variant_id',$request->product_variant_id)->where('product_id',$request->product_id)->first();
        if($cart){
            if($request->action_type == "add"){
                $cart->quantity = $cart->quantity + 1;
                $cart->save();
            }
            if($request->action_type == "remove"){
                if($cart->quantity == 1){
                    $cart->estatus = 3;
                    $cart->save();
                    $cart->delete();
                }
                else {
                    $cart->quantity = $cart->quantity - 1;
                    $cart->save();
                }
            }
        }
        else{
            $cart = new Cart();
            $cart->user_id = $request->user_id;
            $cart->product_variant_id = $request->product_variant_id;
            $cart->product_id = $request->product_id;
            $cart->quantity = ($request->action_type == "add") ? 1 : 0;
            $cart->save();
        }

        $cart_count = Cart::where('user_id',$request->user_id)->where('estatus',1)->where('quantity',"!=",0)->count();
        return $this->sendResponseWithData(['total_cart_items' => $cart_count],"Cart Items Updated Successfully.");
    }

    public function remove_cart(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'product_variant_id' => 'required',
            'product_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $cart = Cart::where('user_id',$request->user_id)->where('product_variant_id',$request->product_variant_id)->where('product_id',$request->product_id)->first();
        if (!$cart){
            return $this->sendError("Cart Item Not Exist.", "Not Found Error", []);
        }
        $cart->estatus = 3;
        $cart->save();
        $cart->delete();

        $cart_count = Cart::where('user_id',$request->user_id)->where('estatus',1)->where('quantity',"!=",0)->count();
        return $this->sendResponseWithData(['total_cart_items' => $cart_count],"Cart Item Deleted Successfully.");
    }

    public function cartitem_list(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $carts = Cart::with('product_variant.attribute_term','product_variant.product.attribute')->where('user_id',$request->user_id)->where('estatus',1)->orderBy('created_at','DESC')->get();
        $carts_arr = array();
        foreach ($carts as $cart){
            $temp = array();
            $temp['user_id'] = $cart->user_id;
            $temp['product_variant_id'] = $cart->product_variant_id;
            $temp['product_id'] = $cart->product_id;
            $temp['quantity'] = $cart->quantity;
            $temp['product_variant'] = isset($cart->product_variant->product_title) ? $cart->product_variant->product_title : null;
            $temp['sale_price'] = isset($cart->product_variant->sale_price) ? $cart->product_variant->sale_price : null;
            $temp['product_variant_images'] = isset($cart->product_variant->variant_images) ? $cart->product_variant->variant_images : null;
            $temp['attribute'] = $cart->product_variant->product->attribute->attribute_name;
            $temp['attribute_value'] = $cart->product_variant->attribute_term->attrterm_name;
            $temp['sale_price_for_premium_member'] = $cart->product_variant->sale_price_for_premium_member;
            $temp['stock'] = $cart->product_variant->stock;
            array_push($carts_arr,$temp);
        }

        $data['cart_items'] = $carts_arr;
        $data['shipping_cost'] = Settings::find(1)->shipping_cost;
        $data['min_order_amount'] = Settings::find(1)->min_order_amount;
        return $this->sendResponseWithData($data,"Cart Items Retrieved Successfully.");
    }
}
