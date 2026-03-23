<?php

namespace App\PedrooAI;

use App\Models\Access\AccessRequest;

class AccessRecommendationEngine
{
    public function recommend(AccessRequest $request): string
    {
        $risk = app(AccessRiskAnalyzer::class)->analyzeRequest($request);

        return match ($risk) {
            'high'   => 'deny',
            'medium' => 'review',
            'low'    => 'approve',
            default  => 'review',
        };
    }
}