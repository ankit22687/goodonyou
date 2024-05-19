<?php

namespace App\Repositories;

use App\Interfaces\RosterBuilderInterface;
use App\Models\Nurse;
use App\Models\Shift;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class RosterBuilderRepository implements RosterBuilderInterface
{
    public static function loadNursesFromFile(string $filename) : Collection
    {
        // Check if file exists
        if (! Storage::exists($filename)) {
            throw new Exception("File {$filename} does not exist");
        }

        // Get the file content
        $content = Storage::get($filename);

        // Decode the JSON content to array
        $nurses = json_decode($content, true);

        // Map the array to Nurse objects
        $nurses = collect($nurses)->map(function ($nurseName) {
            return new Nurse(['name' => $nurseName]);
        });

        return $nurses;
    }

    public static function buildRoster(Collection $nurses, Carbon $startDate, Carbon $endDate) : Collection
    {
        $totalDays = $endDate->diffInDays($startDate);

        $shifts = collect();

        for ($i = 0; $i <= $totalDays; $i++) {
            $shiftDate = $startDate->copy()->addDays($i);

            // Rotate the nurses collection
            $rotatedNurses = self::rotate($nurses, $i * config('roster.nurses_per_shift') * 3);

            // Assign nurses to shifts
            $morningNurses = $rotatedNurses->take(config('roster.nurses_per_shift'));
            $eveningNurses = $rotatedNurses->slice(config('roster.nurses_per_shift'), config('roster.nurses_per_shift'));
            $nightNurses = $rotatedNurses->slice(config('roster.nurses_per_shift') * 2, config('roster.nurses_per_shift'))->take(config('roster.nurses_per_shift'));

            // Create shifts for the day
            $shifts->push(self::createShift($shiftDate, Shift::SHIFT_TYPE_MORNING, $morningNurses));
            $shifts->push(self::createShift($shiftDate, Shift::SHIFT_TYPE_EVENING, $eveningNurses));
            $shifts->push(self::createShift($shiftDate, Shift::SHIFT_TYPE_NIGHT, $nightNurses));
        }

        return $shifts;
    }

    protected static function rotate(Collection $collection, int $offset) : Collection
    {
        $count = $collection->count();
        $offset = $offset % $count;

        return $collection->slice($offset)->merge($collection->take($offset));
    }

    protected static function createShift(Carbon $shiftDate, string $shiftType, Collection $nurses) : Shift
    {
        return new Shift([
            'shift_date' => $shiftDate,
            'type' => $shiftType,
            'nurses' => $nurses,
        ]);
    }
}
