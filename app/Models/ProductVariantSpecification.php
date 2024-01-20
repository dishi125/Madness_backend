<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariantSpecification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'product_variant_specifications';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_variant_id',
        'product_id',
        'attribute_id',
        'attribute_term_id',
        'type',
        'estatus',
    ];

    public function attribute(){
        return $this->hasOne(Attribute::class,'id','attribute_id');
    }

    public function attribute_term(){
        return $this->hasOne(AttributeTerm::class,'id','attribute_term_id');
    }
}
