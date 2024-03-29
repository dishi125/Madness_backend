<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'primary_category_id',
        'child_category_id',
        'subchild_category_id',
        'hsn_code',
        'attrid_for_variation',
        'attr_term_ids',
        'note',
        'estatus',
        'desc'
    ];

    public function primary_category(){
        return $this->hasOne(Category::class,'id','primary_category_id');
    }

    public function child_category(){
        return $this->hasOne(Category::class,'id','child_category_id');
    }

    public function sub_child_category(){
        return $this->hasOne(Category::class,'id','subchild_category_id');
    }

    public function product_variant(){
        return $this->hasMany(ProductVariant::class,'product_id','id');
    }

    public function attribute(){
        return $this->hasOne(Attribute::class,'id','attrid_for_variation');
    }
}
