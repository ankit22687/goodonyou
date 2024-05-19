<?php

namespace App\Console\Commands;

use App\Interfaces\RosterBuilderInterface;
use App\Interfaces\RosterFormatterInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GenerateRoster extends Command
{
    public function __construct(
        private RosterBuilderInterface $rosterBuilder,
        private RosterFormatterInterface $rosterFormatter
    ) {
        parent::__construct();
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-roster
                            {filename : filename of the nurses file to import, in JSON format}
                            {start-date : the first date of the period to roster, in YYYY-MM-DD format}
                            {end-date : a nurses file to import}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a nurses roster';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = $this->argument('filename');
        $startDate = $this->argument('start-date');
        $endDate = $this->argument('end-date');

        if (!$this->validateArguments($filename, $startDate, $endDate)) {
            return;
        }

        $this->info("Generating roster for {$this->argument('filename')} from {$startDate} to {$endDate}");

        $nurses = $this->rosterBuilder->loadNursesFromFile($filename);

        $rosters = $this->rosterBuilder->buildRoster($nurses, Carbon::parse($startDate), Carbon::parse($endDate));

        $this->info(
            $this->rosterFormatter->formatRoster(
                $rosters
            )->join("\n")
        );
    }

    public function validateArguments(string $filename, string $startDate, string $endDate): bool
    {
        $validator = Validator::make([
            'filename' => $filename,
            'start-date' => $startDate,
            'end-date' => $endDate,
        ], [
            'filename' => 'required',
            'start-date' => 'required|date_format:Y-m-d',
            'end-date' => 'required|date_format:Y-m-d|after_or_equal:start-date',
        ]);

        if ($validator->fails()) {
            $this->info('Cannot start command, see errors below:');
        
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return false;
        }

        // Check if file exists
        if (!Storage::exists($filename)) {
            $this->info('Cannot start command:');

            $this->error("File {$filename} does not exist");
            return false;
        }

        return true;
    }
}
