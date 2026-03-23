<?php

namespace App\Services;

use App\Models\RuleSuggestion;

class RuleSuggestionService
{
    public function store(int $breedId, array $rules): void
    {
        foreach ($rules as $rule) {

            RuleSuggestion::create([
                'breed_id' => $breedId,

                'detected_type' => $rule['rule_type'], // pl. color, country
                'raw_value' => $rule['value'],         // pl. black_and_tan

                'suggested_rule' => $this->formatRule($rule), 
                'occurrences' => $rule['confidence'] ?? 0,

                'status' => 'pending',
            ]);
        }
    }

    private function formatRule(array $rule): string
    {
        return "{$rule['rule_key']} {$rule['operator']} {$rule['value']}";
    }
}