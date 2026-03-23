<?php

namespace App\Services\Dashboard;

use App\Models\Dog;
use App\Models\PipelineTask;
use App\Models\LearningQueue;

class DashboardService
{
    public function getStats(): array
    {
        return [
            'dogs_total'        => Dog::count(),
            'pipeline_pending'  => PipelineTask::where('status', 'PENDING')->count(),
            'pipeline_failed'   => PipelineTask::where('status', 'FAILED')->count(),
            'learning_new'      => LearningQueue::where('status', 'NEW')->count(),
            'learning_confirmed'=> LearningQueue::where('status', 'CONFIRMED')->count(),
        ];
    }
}
