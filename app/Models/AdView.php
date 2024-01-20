<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdView extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'ad_views';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'view_name',
        'width',
        'height',
        'estatus',
    ];
}
