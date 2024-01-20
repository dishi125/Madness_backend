<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'order_items';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'order_id',
        'order_return_imgs',
        'order_return_video',
        'order_return_video_thumb',
        'order_action_reason',
        'is_return_requested',
        'payment_status',
        'order_status',
        'updated_by',
        'order_note',
        'tillreturned_date',
        'payment_action_date',
        'estatus',
        'item_details',
    ];

    protected $appends = ['variantid','product_title','item_image','attributename','attributetermname','item_quantity','order_item_price','sub_discount','total_item_amount','item_payable_amt'];

    public function getVariantidAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return $item_details['variantId'];
    }

    public function getProductTitleAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return $item_details['ProductTitle'];
    }

    public function getItemImageAttribute()
    {
        $item_details = json_decode($this->item_details,true);
        $ProductVariant = ProductVariant::where('id',$item_details['variantId'])->first();

        return isset($ProductVariant->variant_images)?$ProductVariant->variant_images[0]:'';
    }

    public function getAttributenameAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return isset($item_details['attribute']) ? $item_details['attribute'] : '';
    }

    public function getAttributetermnameAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return isset($item_details['attributeTerm']) ? $item_details['attributeTerm'] : '';
    }

    public function getItemQuantityAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return isset($item_details['itemQuantity']) ? $item_details['itemQuantity'] : '';
    }

    public function getOrderItemPriceAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return isset($item_details['orderItemPrice']) ? $item_details['orderItemPrice'] : '';
    }

    public function getSubDiscountAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return isset($item_details['SubDiscount']) ? $item_details['SubDiscount'] : '';
    }

    public function getTotalItemAmountAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return isset($item_details['totalItemAmount']) ? $item_details['totalItemAmount'] : '';
    }

    public function getItemPayableAmtAttribute()
    {
        $item_details = json_decode($this->item_details, true);
        return isset($item_details['itemPayableAmt']) ? $item_details['itemPayableAmt'] : '';
    }

    public function order(){
        return $this->hasOne(Order::class,'id','order_id');
    }
}
