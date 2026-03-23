<?php

namespace App\Services\AI;

use App\Models\LearningQueue;
use Illuminate\Support\Collection;

class PatternMinerService
{
    /**
     * Kinyeri a leggyakoribb mintákat a learning queue-ból.
     */
    public function mine(int $limit = 20): Collection
    {
        return LearningQueue::query()
            ->select('detected_type', 'raw_value')
            ->selectRaw('COUNT(*) as occurrences')
            ->groupBy('detected_type', 'raw_value')
            ->orderByDesc('occurrences')
            ->limit($limit)
            ->get();
    }
}