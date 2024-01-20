<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlyCommission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'monthly_commissions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'total_amount',
        'commission_status',
        'current_month',
        'current_year',
        'payment_date',
        'estatus',
    ];

    public function commission(){
        return $this->hasMany(Commission::class,'monthly_commission_id','id');
    }

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
