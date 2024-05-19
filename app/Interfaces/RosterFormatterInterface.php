<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface RosterFormatterInterface
{
    public function formatRoster(Collection $roster): Collection;
}
