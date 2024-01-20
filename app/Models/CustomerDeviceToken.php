<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerDeviceToken extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customer_device_tokens';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'estatus',
    ];
}
