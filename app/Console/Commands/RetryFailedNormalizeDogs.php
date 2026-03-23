<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RetryFailedNormalizeDogs extends Command
{
    protected $signature = 'pedroo:retry-failed-normalize';
    protected $description = 'Retry failed NormalizeDogJob jobs';

    public function handle(): int
    {
        $this->info('Collecting failed NormalizeDogJob jobs...');

        $failed = DB::table('failed_jobs')
            ->where('payload', 'like', '%NormalizeDogJob%')
            ->get();

        if ($failed->isEmpty()) {
            $this->info('No failed NormalizeDogJob jobs found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$failed->count()} failed jobs. Retrying...");

        foreach ($failed as $job) {
            $this->line("Retrying job ID {$job->id}...");
            $this->call('queue:retry', ['id' => $job->id]);
        }

        $this->info('Retry finished.');
        return Command::SUCCESS;
    }
}