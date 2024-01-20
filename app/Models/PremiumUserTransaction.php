<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PremiumUserTransaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'premium_user_transactions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'amount',
        'transaction_id',
        'payment_mode',
        'transaction_date',
        'estatus',
    ];
}
