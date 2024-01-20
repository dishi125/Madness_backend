<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Suggestion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'suggestions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'message',
        'estatus',
    ];

    public function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
