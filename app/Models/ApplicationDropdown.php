<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationDropdown extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'application_dropdowns';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'title',
    ];
}
