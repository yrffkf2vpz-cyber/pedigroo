<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeStatsCommand extends Command
{
    protected $signature = 'pedroo:normalize-stats';
    protected $description = 'Show basic stats about normalization pipeline';

    public function handle(): int
    {
        $sandboxCount = DB::table('pedroo_dogs')->count();
        $finalCount   = DB::table('pd_dogs')->count();

        $failedNormalizeJobs = DB::table('failed_jobs')
            ->where('payload', 'like', '%NormalizeDogJob%')
            ->count();

        $this->info('Pedroo Normalize Stats');
        $this->line('----------------------');
        $this->line("Sandbox dogs (pedroo_dogs): {$sandboxCount}");
        $this->line("Final dogs   (pd_dogs)    : {$finalCount}");
        $this->line("Failed NormalizeDogJob    : {$failedNormalizeJobs}");

        return Command::SUCCESS;
    }
}