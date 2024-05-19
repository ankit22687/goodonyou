<?php

namespace App\Models;

use Jenssegers\Model\Model;

class Shift extends Model {
    CONST SHIFT_TYPE_MORNING = 'morning';
    CONST SHIFT_TYPE_EVENING = 'evening';
    CONST SHIFT_TYPE_NIGHT = 'night';

    protected $fillable = ['date', 'type', 'nurses'];
    
    protected $casts = [
        'date' => 'date',
    ];
}