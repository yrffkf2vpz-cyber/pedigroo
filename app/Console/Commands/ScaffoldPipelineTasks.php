<?php

namespace App\Console\Commands;

use App\Services\Pipeline\PipelineTaskGenerator;
use Illuminate\Console\Command;

class ScaffoldPipelineTasks extends Command
{
    protected $signature = 'pedroo:scaffold-tasks';
    protected $description = 'Generate skeleton classes for all missing pipeline tasks';

    public function handle(): int
    {
        $this->info('Scaffolding missing pipeline tasks...');

        $generator = new PipelineTaskGenerator();
        $result    = $generator->generateMissingTasks();

        $this->info('Generated: ' . count($result['generated']));
        foreach ($result['generated'] as $task) {
            $this->line("  + {$task}");
        }

        $this->info('Existing: ' . count($result['existing']));
        $this->info('Failed: ' . count($result['failed']));

        if (!empty($result['failed'])) {
            foreach ($result['failed'] as $fail) {
                $this->error("  - {$fail['task']}: {$fail['error']}");
            }
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}