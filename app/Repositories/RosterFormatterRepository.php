<?php

namespace App\Repositories;

use App\Interfaces\RosterFormatterInterface;
use App\Models\Nurse;
use App\Models\Shift;
use Illuminate\Support\Collection;

class RosterFormatterRepository implements RosterFormatterInterface
{
    public function formatRoster(Collection $roster): Collection
    {
        return $roster->map(function (Shift $shift) {
            return $this->shiftLine($shift);
        });
    }

    private function shiftLine(Shift $shift): string
    {
        $nurses = $this->nursesForShift($shift->nurses);
        return "{$shift->date->toDateString()} | {$shift->type} | {$nurses}";
    }

    private function nursesForShift(Collection $nurses): string
    {
        return $nurses->map(function (Nurse $nurse) {
            return $nurse->name;
        })->join(', ');
    }
}
