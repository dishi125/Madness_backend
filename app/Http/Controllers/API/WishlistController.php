<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends BaseController
{
    public function update_wishlist(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'product_variant_id' => 'required',
            'product_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $Wishlist = Wishlist::where('user_id',$request->user_id)->where('product_variant_id',$request->product_variant_id)->where('product_id',$request->product_id)->first();
        if($Wishlist){
            $Wishlist->estatus = 3;
            $Wishlist->save();
            $Wishlist->delete();
        }
        else{
            $Wishlist = new Wishlist();
            $Wishlist->user_id = $request->user_id;
            $Wishlist->product_variant_id = $request->product_variant_id;
            $Wishlist->product_id = $request->product_id;
            $Wishlist->save();
        }

        $Wishlist_count = Wishlist::where('user_id',$request->user_id)->where('estatus',1)->count();
        return $this->sendResponseWithData(['total_wishlist_items' => $Wishlist_count],"Wishlist Items Updated Successfully.");
    }

    public function wishlistitem_list(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $Wishlists = Wishlist::with('product_variant')->where('user_id',$request->user_id)->where('estatus',1)->orderBy('created_at','DESC')->get();
        $Wishlists_arr = array();
        foreach ($Wishlists as $Wishlist){
            $temp = array();
            $temp['user_id'] = $Wishlist->user_id;
            $temp['product_variant_id'] = $Wishlist->product_variant_id;
            $temp['product_id'] = $Wishlist->product_id;
            $temp['product_variant'] = isset($Wishlist->product_variant->product_title) ? $Wishlist->product_variant->product_title : null;
            $temp['sale_price'] = isset($Wishlist->product_variant->sale_price) ? $Wishlist->product_variant->sale_price : null;
            $temp['sale_price_for_premium_member'] = isset($Wishlist->product_variant->sale_price_for_premium_member) ? $Wishlist->product_variant->sale_price_for_premium_member : null;
            $temp['product_variant_images'] = isset($Wishlist->product_variant->variant_images) ? $Wishlist->product_variant->variant_images : null;
            array_push($Wishlists_arr,$temp);
        }
        return $this->sendResponseWithData($Wishlists_arr,"Wishlist Items Retrieved Successfully.");
    }

}
