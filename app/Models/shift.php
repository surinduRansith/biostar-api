<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class shift extends Model
{
    protected $fillable = [
        'shiftname',
        'start_time',
        'end_time'
        
    ];
}
