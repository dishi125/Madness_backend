<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Commission;
use App\Models\Coupon;
use App\Models\Level;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    public function create_order(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'total_ordercost' => 'required',
            'payble_ordercost' => 'required',
            'payment_type' => 'required',
            'payment_currency' => 'required',
            'payment_date' => 'required',
            'payment_status' => 'required',
            'delivery_address' => 'required',
            'order_items' => 'required',
        ]);

//        dd($validator->errors()->messages());
        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $order_items = json_decode($request->order_items, true);

        $last_order_id = Order::orderBy('id','desc')->pluck('id')->first();
        if(isset($last_order_id)) {
            $last_order_id = $last_order_id + 1;
            $len_last_order_id = strlen($last_order_id);
            if($len_last_order_id == 1){
                $last_order_id = "000".$last_order_id;
            }
            elseif($len_last_order_id == 2){
                $last_order_id = "00".$last_order_id;
            }
            elseif($len_last_order_id == 3){
                $last_order_id = "0".$last_order_id;
            }
        }
        else{
            $last_order_id = "0001";
        }

        //Save Order Data
        $order = new Order();
        $order->user_id = $request->user_id;
        $order->custom_orderid = Carbon::now()->format('ymd') . $last_order_id;
        $order->sub_totalcost = isset($request->sub_totalcost) ? $request->sub_totalcost: null;
        $order->shipping_charge = isset($request->shipping_charge) ? $request->shipping_charge : null;
        $order->discount_amount = isset($request->discount_amount) ? $request->discount_amount : null;
        $order->coupan_code_id = isset($request->coupan_code_id) ? $request->coupan_code_id : null;
        $order->total_ordercost = isset($request->total_ordercost) ? $request->total_ordercost : null;
        $order->payble_ordercost = isset($request->payble_ordercost) ? $request->payble_ordercost : null;
        $order->payment_type = isset($request->payment_type) ? $request->payment_type : null;
        $order->payment_transaction_id = isset($request->payment_transaction_id) ? $request->payment_transaction_id : null;
        $order->payment_currency = isset($request->payment_currency) ? $request->payment_currency : null;
        $order->gateway_name = isset($request->gateway_name) ? $request->gateway_name : null;
        $order->payment_mode = isset($request->payment_mode) ? $request->payment_mode : null;
        $order->payment_date = isset($request->payment_date) ? $request->payment_date : null;
        $order->payment_status = isset($request->payment_status) ? $request->payment_status : null;
        $order->delivery_address = isset($request->delivery_address) ? $request->delivery_address : null;
        $order->order_note = $order_items['order_note'];
        $order->order_status = $order_items['order_status'];
        $order->save();

        //Save Order Item Data
        foreach ($order_items['item_details'] as $order_item){
            $OrderItem = new OrderItem();
            $OrderItem->order_id = $order->id;
            $OrderItem->payment_status = $order_items['payment_status'];
            $OrderItem->order_status = $order_items['order_status'];
            $OrderItem->updated_by = $order_items['updated_by'];
            $OrderItem->order_note = $order_items['order_note'];
            $product_title = ProductVariant::where('id',$order_item['variantId'])->pluck('product_title')->first();
            $order_item['ProductTitle'] = $product_title;
            $OrderItem->item_details = json_encode($order_item);
            $OrderItem->payment_action_date = $order_items['payment_action_date'];
            $OrderItem->save();

            $carts = Cart::where('user_id',$request->user_id)->where('product_variant_id',$order_item['variantId'])->get();
            foreach ($carts as $cart){
                $cart->estatus = 3;
                $cart->save();
                $cart->delete();
            }
        }

        //Save Commission Data
        $user_id = User::where('id',$request->user_id)->pluck('parent_user_id')->first();
        $levels = Level::get();
        foreach ($levels as $level){
            if($user_id != 0) {
                $is_premium = User::where('id',$user_id)->where('estatus',1)->where('is_premium',1)->first();
                if ($is_premium) {
                    $commission = new Commission();
                    $commission->user_id = $user_id;
                    $commission->order_id = $order->id;
                    $commission->level_id = $level->id;
                    $commission->amount = ($order->payble_ordercost * $level->commission_percentage) / 100;
                    $commission->save();
                    $user_id = User::where('id', $commission->user_id)->pluck('parent_user_id')->first();
                }
            }
        }

        return $this->sendResponseSuccess("Order Submitted Successfully");
    }

    public function order_list(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $orders = Order::with('order_item')->where('user_id',$request->user_id);
        if(isset($request->order_status) && $request->order_status!=0){
            $order_status = explode(",",$request->order_status);
            $orders =  $orders->whereIn('order_status',$order_status);
        }
        $orders = $orders->orderBy('created_at','DESC')->paginate(10);

        $orders_arr = array();
        foreach ($orders as $order){
            $temp['order_id'] = $order['id'];
            $temp['custom_orderid'] = $order['custom_orderid'];
            $temp['order_status'] = $order['order_status'];
            $temp['total_ordercost'] = $order['total_ordercost'];
            $temp['order_date'] = date('d-m-Y h:i A', strtotime($order->created_at));
            $temp['payment_status'] = $order['payment_status'];
            $temp['payment_type'] = $order['payment_type'];
            $temp['total_items'] = count($order['order_item']);
            $temp['delivery_date'] = $order['delivery_date'];
            $temp['item_list'] = array();
            foreach ($order->order_item as $order_item){
                $item_details = json_decode($order_item->item_details, true);
                $temp_order_item['product_title']= $item_details['ProductTitle'];
                $temp_order_item['quantity']= $item_details['itemQuantity'];
                $ProductVariant = ProductVariant::where('id',$item_details['variantId'])->first();
                $temp_order_item['image']= isset($ProductVariant->variant_images) ? $ProductVariant->variant_images[0] : null;
                array_push($temp['item_list'],$temp_order_item);
            }
            array_push($orders_arr,$temp);
        }

        $data['orders'] = $orders_arr;
        $data['total_records'] = $orders->toArray()['total'];
        return $this->sendResponseWithData($data,"All Orders Retrieved Successfully.");
    }

    public function order_details(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'order_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $order = Order::with('order_item')->where('id',$request->order_id)->where('user_id',$request->user_id)->first();
        if(!$order){
            return $this->sendError('Order Not Exist',"Not Found Error", []);
        }

        $order_arr = array();
        $order_arr['user_id'] = $order->user_id;
        $order_arr['custom_orderid'] = $order->custom_orderid;
        $order_arr['sub_totalcost'] = $order->sub_totalcost;
        $order_arr['shipping_charge'] = $order->shipping_charge;
        $order_arr['discount_amount'] = $order->discount_amount;
        $order_arr['coupan_code_id'] = $order->coupan_code_id;
        $order_arr['total_ordercost'] = $order->total_ordercost;
        $order_arr['payble_ordercost'] = $order->payble_ordercost;
        $order_arr['payment_type'] = $order->payment_type;
        $order_arr['payment_transaction_id'] = $order->payment_transaction_id;
        $order_arr['payment_currency'] = $order->payment_currency;
        $order_arr['gateway_name'] = $order->gateway_name;
        $order_arr['payment_mode'] = $order->payment_mode;
        $order_arr['payment_date'] = $order->payment_date;
        $order_arr['payment_status'] = $order->payment_status;
        $order_arr['delivery_address'] = json_decode($order->delivery_address,true);
        $order_arr['order_rating'] = $order->order_rating;
        $order_arr['order_note'] = $order->order_note;
        $order_arr['order_status'] = $order->order_status;
        $order_arr['delivery_date'] = $order->delivery_date;
        $order_arr['order_date'] = date('d-m-Y h:i A', strtotime($order->created_at));
        $order_arr['order_items'] = array();
        foreach ($order->order_item as $order_item){
            $temp = array();
            $temp['order_item_id'] = $order_item->id;
            $temp['order_id'] = $order_item->order_id;
            $temp['payment_status'] = $order_item->payment_status;
            $temp['order_status'] = $order_item->order_status;
            $temp['updated_by'] = $order_item->updated_by;
            $temp['order_note'] = $order_item->order_note;
            $temp['item_details'] = json_decode($order_item->item_details,true);
            $temp['item_image'] = $order_item->item_image;
            $temp['payment_action_date'] = $order_item->payment_action_date;
            $temp['tillreturned_date'] = $order_item->tillreturned_date;
            $temp['can_return'] = false;
            if(Carbon::now() <= $order_item->tillreturned_date) {
                $temp['can_return'] = true;
            }
            array_push($order_arr['order_items'], $temp);
        }

        $data['order_details'] = $order_arr;
        return $this->sendResponseWithData($data,"Order Details Retrieved Successfully.");
    }

    public function apply_coupon(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'coupon_code' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $coupon = Coupon::with('discount_type')->where('coupon_code',$request->coupon_code)->where('expiry_date',">",Carbon::now()->toDateString())->where('estatus',1)->first();
        if(!$coupon){
            return $this->sendError('Coupon not Exist',"Invalid Coupon", []);
        }

        $order = Order::where('user_id',$request->user_id)->where('coupan_code_id',$coupon->id)->get();
        if ($coupon->usage_per_user > count($order)){
            $data = array();
            $temp_coupon['coupan_code_id'] = $coupon->id;
            $temp_coupon['discount_type'] = $coupon->discount_type->title;
            $temp_coupon['coupon_amount'] = $coupon->coupon_amount;
            $temp_coupon['allow_cod'] = $coupon->allow_cod;
            array_push($data,$temp_coupon);
            return $this->sendResponseWithData($data,"Coupon Applied Successfully");
        }
        return $this->sendError("You can not use this Coupon","Usage Limit Over", []);
    }

    function update_order_status(Request $request){
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'order_status' => 'required',
            'return_request_images.*' => 'image|mimes:jpeg,png,jpg',
            'return_request_video' => 'mimes:mp4,ogx,oga,ogv,ogg,webm',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $order = Order::find($request->order_id);
        $old_order_status = $order->order_status;
        if (!$order){
            return $this->sendError("Order Not Exist", "Not Found Error", []);
        }

        if ($request->order_status == "return_request"){
            if(isset($request->order_item_id) && $request->order_item_id!=0){
                $order_item = OrderItem::where('id',$request->order_item_id)->where('order_id',$request->order_id)->first();
                if (!$order_item){
                    return $this->sendError("Order Item Not Exist", "Not Found Error", []);
                }
                $order_item->order_status = 4;

                if($request->hasFile('return_request_images')) {
                    $return_request_images = array();
                    foreach ($request->file('return_request_images') as $image) {
                        $image_name = 'Return_request_image_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = public_path('images/return_request_images');
                        $image->move($destinationPath, $image_name);
                        array_push($return_request_images,'images/return_request_images/'.$image_name);
                    }

                    $order_item->order_return_imgs = implode(",",$return_request_images);
                }

                if ($request->hasFile('return_request_video')){
                    $image = $request->file('return_request_video');
                    $image_name = 'Return_request_video_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('images/return_request_videos');
                    $image->move($destinationPath, $image_name);
                    $order_item->order_return_video = 'images/return_request_videos/'.$image_name;
                }
                $order_item->order_action_reason = $request->reason;
                $order_item->save();
            }else{
                $order->order_status = 4;
                if($request->hasFile('return_request_images')) {
                    $return_request_images = array();
                    foreach ($request->file('return_request_images') as $image) {
                        $image_name = 'Return_request_image_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                        $destinationPath = public_path('images/return_request_images');
                        $image->move($destinationPath, $image_name);
                        array_push($return_request_images,'images/return_request_images/'.$image_name);
                    }

                    $order->order_return_imgs = implode(",",$return_request_images);
                }

                if ($request->hasFile('return_request_video')){
                    $image = $request->file('return_request_video');
                    $image_name = 'Return_request_video_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('images/return_request_videos');
                    $image->move($destinationPath, $image_name);
                    $order->order_return_video = 'images/return_request_videos/'.$image_name;
                }
                $order->order_action_reason = $request->reason;
                $order->save();
            }
        }
        elseif ($request->order_status == "cancel"){
            $order->order_status = 7;
            $order->save();

            //For cancel commission amount in case of cancel order
            if ($old_order_status==1 && $order->order_status==7){
                $commissions = Commission::where('order_id',$order->id)->get();
                foreach ($commissions as $commission){
                    $commission->commission_status = 4;
                    $commission->save();
                }
            }
        }
        else{
            return $this->sendError("Please Provide Valid Order Status", "Invalid Order Status", []);
        }

        return $this->sendResponseSuccess("Order Status Updated Successfully");
    }
}
