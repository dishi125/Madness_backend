<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'settings';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_discount_percentage',
        'shipping_cost',
        'premium_user_membership_fee',
        'min_order_amount',
        'estatus',
    ];
}
