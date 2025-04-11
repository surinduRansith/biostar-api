<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bs2users extends Model
{
    protected $fillable = [
        'userid',
        'name',
        'image'
    ];
}
