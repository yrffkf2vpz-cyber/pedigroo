<?php

namespace App\PedrooAI;

use App\Models\Access\AccessRequest;
use App\Models\Access\UserTrustScore;

class AccessRiskAnalyzer
{
    public function analyzeRequest(AccessRequest $request): string
    {
        $trust = UserTrustScore::where('user_id', $request->requester_user_id)->first();

        // 1) Red trust ? automatikus high risk
        if ($trust && $trust->level === 'red') {
            return 'high';
        }

        // 2) Túl sok kérés rövid ido alatt
        $recentRequests = AccessRequest::where('requester_user_id', $request->requester_user_id)
            ->where('created_at', '>=', now()->subHours(6))
            ->count();

        if ($recentRequests > 5) {
            return 'medium';
        }

        // 3) Default
        return 'low';
    }
}