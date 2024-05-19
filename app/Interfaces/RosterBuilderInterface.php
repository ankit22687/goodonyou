<?php

namespace App\Interfaces;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface RosterBuilderInterface
{
    public static function loadNursesFromFile(string $filename): Collection;

    public static function buildRoster(Collection $nurses, Carbon $startDate, Carbon $endDate): Collection;
}
