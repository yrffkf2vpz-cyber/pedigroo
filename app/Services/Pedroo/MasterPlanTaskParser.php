<?php

namespace App\Services\Pedroo;

use App\Models\PipelineTask;

class MasterPlanTaskParser
{
    public function parse(): array
    {
        $plan = config('pedroo_plan');

        if (!$plan || !is_array($plan)) {
            return ['error' => 'pedroo_plan config not found or invalid'];
        }

        $created = [];

        foreach ($plan as $moduleKey => $moduleData) {

            if (!isset($moduleData['tasks'])) {
                continue;
            }

            foreach ($moduleData['tasks'] as $taskKey => $isDone) {

                // Csak a h?tral?vo feladatokb?l gener?lunk taskot
                if ($isDone === true) {
                    continue;
                }

                $type = "{$moduleKey}:{$taskKey}";

                $task = PipelineTask::create([
                    'type'    => $type,
                    'payload' => json_encode([
                        'module' => $moduleKey,
                        'task'   => $taskKey,
                        'title'  => $moduleData['title'] ?? $moduleKey,
                    ]),
                    'status'  => 'pending',
                ]);

                $created[] = $task;
            }
        }

        return [
            'created_count' => count($created),
            'tasks'         => $created,
        ];
    }
}
