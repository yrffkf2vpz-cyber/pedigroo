<?php

namespace App\Services\Pipeline;

use App\Models\PipelineTask;

class PipelineDashboardService
{
    public function getRecentTasks(int $limit = 50)
    {
        return PipelineTask::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
