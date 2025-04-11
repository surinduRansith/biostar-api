<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class shiftByUsers extends Model
{
    
    protected $fillable = [
        'shift_id',
        'user_id',
        'name'
        
        
    ];
}
