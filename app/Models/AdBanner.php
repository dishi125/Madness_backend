<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdBanner extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ad_banners';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'ad_group_id',
        'ad_title',
        'image',
        'application_dropdown_id',
        'value',
        'product_variant_id',
        'estatus',
    ];

    public function applicationdropdown(){
        return $this->hasOne(ApplicationDropdown::class,'id','application_dropdown_id');
    }
}
