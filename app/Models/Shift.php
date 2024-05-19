<?php

namespace App\Models;

use Jenssegers\Model\Model;

class Shift extends Model
{
    public const SHIFT_TYPE_MORNING = 'morning';

    public const SHIFT_TYPE_EVENING = 'evening';

    public const SHIFT_TYPE_NIGHT = 'night';

    protected $fillable = ['shift_date', 'type', 'nurses'];

    protected $casts = [
        'shift_date' => 'date',
    ];
}
