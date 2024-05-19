<?php
namespace Tests\Unit;

use App\Models\Nurse;
use App\Models\Shift;
use App\Repositories\RosterFormatterRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RosterFormatterRepositoryTest extends TestCase
{
    protected $rosterFormatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rosterFormatter = new RosterFormatterRepository();
    }

    public function testFormatRoster()
    {
        // Arrange
        $shift = new Shift([
            'date' => Carbon::parse('2022-01-01'),
            'type' => 'day',
            'nurses' => new Collection([
                new Nurse(['name' => 'Iskra']),
                new Nurse(['name' => 'Andronicus']),
            ]),
        ]);
        $roster = new Collection([$shift]);

        // Act
        $result = $this->rosterFormatter->formatRoster($roster);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals('2022-01-01 | day | Iskra, Andronicus', $result->first());
    }
}
