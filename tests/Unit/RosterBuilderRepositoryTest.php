<?php

namespace Tests\Unit;

use App\Models\Nurse;
use App\Models\Shift;
use App\Repositories\RosterBuilderRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RosterBuilderRepositoryTest extends TestCase
{
    public function testLoadNursesFromFile()
    {
        // Arrange
        Storage::fake('local');
        $filename = 'test.json';
        $nurses = ['Iskra', 'Andronicus', 'Tipene', 'Jaroslav'];
        Storage::put($filename, json_encode($nurses));

        // Act
        $result = RosterBuilderRepository::loadNursesFromFile($filename);

        // Assert
        $this->assertCount(4, $result);
        $this->assertInstanceOf(Nurse::class, $result->first());
        $this->assertEquals('Iskra', $result->first()->name);
    }

    public function testBuildRoster()
    {
        // Arrange
        $nurses = new Collection([
            ['name' => 'Nurse A'],
            ['name' => 'Nurse B'],
            ['name' => 'Nurse C'],
            ['name' => 'Nurse D'],
            ['name' => 'Nurse E'],
            ['name' => 'Nurse F'],
            ['name' => 'Nurse G'],
            ['name' => 'Nurse H'],
            ['name' => 'Nurse I'],
            ['name' => 'Nurse J'],
        ]);
        $startDate = Carbon::parse('2022-01-01');
        $endDate = Carbon::parse('2022-01-31');

        // Act
        $result = RosterBuilderRepository::buildRoster($nurses, $startDate, $endDate);

        // Assert
        $this->assertInstanceOf(Collection::class, $result);

        foreach ($result as $shift) {
            $this->assertInstanceof(Shift::class, $shift);
            $this->assertArrayHasKey('shift_date', $shift);
            $this->assertArrayHasKey('type', $shift);
            $this->assertArrayHasKey('nurses', $shift);
            $this->assertInstanceOf(Carbon::class, $shift['shift_date']);
            $this->assertContains($shift['type'], [Shift::SHIFT_TYPE_MORNING, Shift::SHIFT_TYPE_EVENING, Shift::SHIFT_TYPE_NIGHT]);
            $this->assertInstanceOf(Collection::class, $shift['nurses']);
        }
    }

    public function testOneNurseOneShiftPerDay()
    {
        // Arrange
        $nurses = new Collection([
            ['name' => 'Nurse A'],
            ['name' => 'Nurse B'],
            ['name' => 'Nurse C'],
            ['name' => 'Nurse D'],
            ['name' => 'Nurse E'],
            ['name' => 'Nurse F'],
            ['name' => 'Nurse G'],
            ['name' => 'Nurse H'],
            ['name' => 'Nurse I'],
            ['name' => 'Nurse J'],
        ]);

        $startDate = Carbon::parse('2024-05-01');
        $endDate = Carbon::parse('2024-05-05');

        // Act
        $result = RosterBuilderRepository::buildRoster($nurses, $startDate, $endDate);

        // Assert
        $shiftsForDay = $result->groupBy('shift_date')->first();
        foreach ($shiftsForDay as $shift) {
            $nurseNames = $shift['nurses']->pluck('name');
            $this->assertCount($nurseNames->count(), $nurseNames->unique());
        }
    }
}
