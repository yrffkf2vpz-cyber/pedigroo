<?php

namespace App\PedrooAI;

use App\Models\Access\AccessRequest;

class AccessPatternDetector
{
    public function detectPatterns(int $userId): array
    {
        $requests = AccessRequest::where('requester_user_id', $userId)->get();

        return [
            'request_count' => $requests->count(),
            'denied_ratio'  => $requests->where('status', 'denied')->count() / max(1, $requests->count()),
            'repeat_targets' => $requests->groupBy('kennel_id')->map->count()->filter(fn($c) => $c > 3)->keys(),
        ];
    }
}