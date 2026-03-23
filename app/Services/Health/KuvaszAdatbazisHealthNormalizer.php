<?php

namespace App\Services\Health;

use App\Dto\HealthRecord;
use App\Models\Dog;

class KuvaszAdatbazisHealthNormalizer implements HealthNormalizer
{
    public function normalize(string $dogId): array
    {
        $dog = Dog::findOrFail($dogId);

        // TODO: scraper + HTML parse
        // Itt most skeleton, hogy a pipeline működjön:

        $rawHealthData = [
            // Példa nyers adatok (később scraper tölti)
            "HD-A",
            "ED 0/0",
            "DM N/N",
        ];

        $normalized = [];

        foreach ($rawHealthData as $raw) {
            $normalized[] = $this->normalizeSingle($raw, $dog->source);
        }

        return $normalized;
    }

    protected function normalizeSingle(string $raw, string $source): HealthRecord
    {
        $raw = trim($raw);

        // HD
        if (preg_match('/HD[-\s:]?([A-E])/', $raw, $m)) {
            return new HealthRecord(
                type: 'HD',
                value: $m[1],
                date: null,
                lab: null,
                source: $source
            );
        }

        // ED
        if (preg_match('/ED[-\s:]?([0-3]\/[0-3])/', $raw, $m)) {
            return new HealthRecord(
                type: 'ED',
                value: $m[1],
                date: null,
                lab: null,
                source: $source
            );
        }

        // DM
        if (preg_match('/DM[-\s:]?(N\/N|N\/A|A\/A)/i', $raw, $m)) {
            return new HealthRecord(
                type: 'DM',
                value: strtoupper($m[1]),
                date: null,
                lab: null,
                source: $source
            );
        }

        // fallback
        return new HealthRecord(
            type: 'UNKNOWN',
            value: $raw,
            date: null,
            lab: null,
            source: $source
        );
    }
}