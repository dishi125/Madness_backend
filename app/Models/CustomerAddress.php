<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customer_addresses';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'is_default',
        'address_title',
        'full_name',
        'mobile_no',
        'pincode',
        'address',
        'address2',
        'landmark',
        'city',
        'state',
        'country',
        'estatus',
    ];
}
