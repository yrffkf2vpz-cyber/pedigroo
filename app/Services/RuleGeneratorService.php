<?php

namespace App\Services;

class RuleGeneratorService
{
    public function generate(array $patterns): array
    {
        $rules = [];

        // Colors
        if (isset($patterns['colors'])) {
            $rules = array_merge($rules, $this->generateColorRules($patterns['colors']));
        }

        // Countries
        if (isset($patterns['origin_countries'])) {
            $rules = array_merge($rules, $this->generateCountryRules($patterns['origin_countries']));
        }

        return $rules;
    }

    private function generateColorRules(array $colorCounts): array
    {
        $total = array_sum($colorCounts);
        $rules = [];

        foreach ($colorCounts as $color => $count) {
            $ratio = $count / $total;

            if ($ratio > 0.7) {
                $rules[] = [
                    'rule_key' => 'dominant_color',
                    'rule_type' => 'color',
                    'operator' => '=',
                    'value' => $color,
                    'confidence' => $ratio,
                ];
            }

            if ($ratio < 0.05) {
                $rules[] = [
                    'rule_key' => 'rare_color',
                    'rule_type' => 'color',
                    'operator' => 'rare',
                    'value' => $color,
                    'confidence' => $ratio,
                ];
            }
        }

        return $rules;
    }

    private function generateCountryRules(array $countryCounts): array
    {
        $total = array_sum($countryCounts);
        $rules = [];

        foreach ($countryCounts as $country => $count) {
            $ratio = $count / $total;

            if ($ratio > 0.6) {
                $rules[] = [
                    'rule_key' => 'typical_origin',
                    'rule_type' => 'country',
                    'operator' => '=',
                    'value' => $country,
                    'confidence' => $ratio,
                ];
            }

            if ($ratio < 0.03) {
                $rules[] = [
                    'rule_key' => 'rare_origin',
                    'rule_type' => 'country',
                    'operator' => 'rare',
                    'value' => $country,
                    'confidence' => $ratio,
                ];
            }
        }

        return $rules;
    }
}