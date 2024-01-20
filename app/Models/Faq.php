<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'faqs';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'question',
        'answer',
        'estatus',
    ];
}
