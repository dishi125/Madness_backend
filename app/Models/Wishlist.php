<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wishlist extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'wishlists';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'product_variant_id',
        'product_id',
        'estatus',
    ];

    public function product_variant(){
        return $this->hasOne(ProductVariant::class,'id','product_variant_id');
    }
}
