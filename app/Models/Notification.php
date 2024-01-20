<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'notifications';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'user_id',
        'notify_title',
        'notify_desc',
        'notify_thumb',
        'application_dropdown_id',
        'value',
        'parent_value',
        'type',
        'estatus',
    ];

    public function applicationdropdown(){
        return $this->hasOne(ApplicationDropdown::class,'id','application_dropdown_id');
    }
}
