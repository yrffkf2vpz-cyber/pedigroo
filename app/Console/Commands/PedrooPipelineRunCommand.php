<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PipelineTask;
use App\Services\Pedroo\TaskExecutor;

class PedrooPipelineRunCommand extends Command
{
    protected $signature = 'pedroo:pipeline';
    protected $description = 'Futtatja a teljes Pedroo pipeline-t pending taskok alapján';

    public function handle()
    {
        $this->info("\n=== PEDROO PIPELINE RUN ===\n");

        $executor = app(TaskExecutor::class);

        while (true) {

            $task = PipelineTask::where('status', 'pending')->orderBy('id')->first();

            if (!$task) {
                $this->info("Nincs több pending task. Kész.\n");
                return Command::SUCCESS;
            }

            $this->info("Task: {$task->type}");

            $task->update(['status' => 'running']);

            $response = $executor->run($task);

            $task->update([
                'log'    => $response,
                'status' => 'done',
            ]);

            $this->info($response);
            $this->info("Task kész.\n");
        }
    }
}