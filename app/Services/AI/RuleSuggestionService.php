<?php

namespace App\Services\AI;

use App\Models\RuleSuggestion;
use Illuminate\Support\Collection;

class RuleSuggestionService
{
    /**
     * A Rule Generator által adott szabályjavaslatok mentése.
     */
    public function saveSuggestions(Collection $rules, int $breedId = null): void
    {
        foreach ($rules as $rule) {
            RuleSuggestion::updateOrCreate(
                [
                    'detected_type' => $rule['type'],
                    'raw_value'     => $rule['raw_value'],
                    'breed_id'      => $breedId,
                ],
                [
                    'suggested_rule' => $rule['suggested_rule'],
                    'occurrences'    => $rule['occurrences'],
                    'status'         => 'pending',
                ]
            );
        }
    }
}