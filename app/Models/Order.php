<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'orders';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'custom_orderid',
        'sub_totalcost',
        'shipping_charge',
        'discount_amount',
        'coupan_code_id',
        'total_ordercost',
        'payble_ordercost',
        'payment_type',
        'payment_transaction_id',
        'payment_currency',
        'gateway_name',
        'payment_mode',
        'payment_date',
        'payment_status',
        'delivery_address',
        'order_rating',
        'order_note',
        'order_status',
        'delivery_date',
        'total_refund_amount',
        'estatus',
    ];

    public function order_item(){
        return $this->hasMany(OrderItem::class,'order_id','id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
