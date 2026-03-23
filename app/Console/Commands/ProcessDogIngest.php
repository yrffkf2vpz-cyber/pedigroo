<?php

namespace App\Console\Commands;

use App\Jobs\ProcessDogIngestTask;
use App\Models\DogIngestTask;
use Illuminate\Console\Command;

class ProcessDogIngest extends Command
{
    protected $signature = 'dog-ingest:process {--limit=50}';
    protected $description = 'Process pending dog ingest tasks';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $tasks = DogIngestTask::where('status', 'pending')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No pending tasks.');
            return self::SUCCESS;
        }

        foreach ($tasks as $task) {
            ProcessDogIngestTask::dispatch($task->id);
        }

        $this->info('Dispatched '.$tasks->count().' tasks.');

        return self::SUCCESS;
    }
}
