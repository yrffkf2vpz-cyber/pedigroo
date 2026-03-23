<?php

namespace App\Services\Pedroo;

use App\Models\PipelineTask;

class TaskExecutor
{
    public function run(PipelineTask $task): string
    {
        try {
            $payload = json_decode($task->payload, true) ?? [];

            $module = $payload['module'] ?? null;
            $taskKey = $payload['task'] ?? null;

            if (!$module || !$taskKey) {
                return 'Missing module or task in payload.';
            }

            // NORMALIZE MODULE
            if ($module === 'normalize') {
                return $this->runNormalizer($taskKey, $payload);
            }

            // TEMPLATE GENERATION MODULE
            return app(TemplateEngine::class)->generate($module, $taskKey, $payload);

        } catch (\Throwable $e) {

            // A task SOHA többé nem ragad be
            $task->update([
                'status' => 'failed',
                'log'    => $e->getMessage(),
            ]);

            return "ERROR: " . $e->getMessage();
        }
    }


    private function runNormalizer(string $taskKey, array $payload): string
    {
        if ($taskKey === 'dogs') {
            return app(\App\Pedroo\Normalize\DogsPipelineStep::class)
                ->handle($payload['rows'] ?? []);
        }

        return "Unknown normalize task: $taskKey";
    }
}
