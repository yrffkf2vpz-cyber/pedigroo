<?php

namespace App\Services\Health;

use App\Models\HealthRecord;

class HealthNormalizer
{
    public function normalize(string $dogId): array
    {
        $rules = config('health_rules');

        $raw = $this->loadRawHealthData($dogId);

        return [
            'HD'     => $this->normalizeField($raw['HD'] ?? null, $rules['HD']),
            'ED'     => $this->normalizeField($raw['ED'] ?? null, $rules['ED']),
            'DM'     => $this->normalizeField($raw['DM'] ?? null, $rules['DM']),
            'MDR1'   => $this->normalizeField($raw['MDR1'] ?? null, $rules['MDR1']),
            'GENETIC' => $this->normalizeGeneric($raw['GENETIC'] ?? null, $rules['GENERIC']),
        ];
    }

    private function loadRawHealthData(string $dogId): array
    {
        $record = HealthRecord::where('dog_id', $dogId)->first();

        if (!$record) {
            return [];
        }

        return $record->toArray();
    }

    private function normalizeField(?string $value, array $ruleSet): string
    {
        if (!$value) {
            return $ruleSet['fallback'];
        }

        foreach ($ruleSet['patterns'] as $pattern => $normalized) {
            if (preg_match($pattern, $value)) {
                return $normalized;
            }
        }

        return $ruleSet['fallback'];
    }

    private function normalizeGeneric(?string $value, array $rules): string
    {
        if (!$value) {
            return $rules['fallback'];
        }

        $clean = strtolower(trim($value));

        if (in_array($clean, $rules['clear'])) {
            return 'CLEAR';
        }

        if (in_array($clean, $rules['carrier'])) {
            return 'CARRIER';
        }

        if (in_array($clean, $rules['affected'])) {
            return 'AFFECTED';
        }

        return $rules['fallback'];
    }
}