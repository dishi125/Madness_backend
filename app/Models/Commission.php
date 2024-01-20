<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'commissions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'order_id',
        'amount',
        'commission_status',
        'level_id',
        'monthly_commission_id',
        'estatus',
    ];

    public function monthly_commission(){
        return $this->hasOne(MonthlyCommission::class,'id','monthly_commission_id');
    }

    public function order(){
        return $this->hasOne(Order::class,'id','order_id');
    }

    public function level(){
        return $this->hasOne(Level::class,'id','level_id');
    }
}
