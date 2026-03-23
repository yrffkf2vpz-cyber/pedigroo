<?php

namespace App\Tasks\System;

use App\Services\Pipeline\PipelineTaskGenerator;
use App\Tasks\Task;

class ScaffoldMissingTasks extends Task
{
    public function handle()
    {
        $generator = new PipelineTaskGenerator();
        $result    = $generator->generateMissingTasks();

        return [
            'generated' => $result['generated'],
            'existing'  => $result['existing'],
            'failed'    => $result['failed'],
        ];
    }
}