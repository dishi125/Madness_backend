<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ad_groups';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'category_id',
        'group_title',
        'group_bg_color',
        'display_adtitle_with_banner',
        'ad_view_id',
        'banner_view',
        'estatus',
    ];

    public function category(){
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function adview(){
        return $this->hasOne(AdView::class,'id','ad_view_id');
    }

    public function adbanner(){
        return $this->hasMany(AdBanner::class,'ad_group_id','id');
    }
}
