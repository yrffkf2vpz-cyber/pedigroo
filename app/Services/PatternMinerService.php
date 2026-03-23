<?php

namespace App\Services;

class PatternMinerService
{
    public function mine(array $dogs): array
    {
        return [
            'colors' => $this->countValues($dogs, 'color'),
            'birth_colors' => $this->countValues($dogs, 'birth_color'),
            'official_colors' => $this->countValues($dogs, 'official_color'),

            'origin_countries' => $this->countValues($dogs, 'origin_country'),
            'standing_countries' => $this->countValues($dogs, 'standing_country'),

            'sex_distribution' => $this->countValues($dogs, 'sex'),
            'status_distribution' => $this->countValues($dogs, 'status'),
        ];
    }

    private function countValues(array $dogs, string $key): array
    {
        $counts = [];

        foreach ($dogs as $dog) {
            if (!isset($dog[$key]) || !$dog[$key]) {
                continue;
            }

            $value = $dog[$key];
            $counts[$value] = ($counts[$value] ?? 0) + 1;
        }

        arsort($counts);

        return $counts;
    }
}