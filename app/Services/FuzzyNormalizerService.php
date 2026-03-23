<?php

namespace App\Services;

class FuzzyNormalizerService
{
    public function normalize(array $data): array
    {
        return [
            'color' => $this->normalizeColor($data['color'] ?? null),
            'birth_color' => $this->normalizeColor($data['birth_color'] ?? null),
            'official_color' => $this->normalizeColor($data['official_color'] ?? null),

            'origin_country' => $this->normalizeCountry($data['origin_country'] ?? null),
            'standing_country' => $this->normalizeCountry($data['standing_country'] ?? null),

            'name' => $this->normalizeName($data['name'] ?? null),
        ];
    }

    private function normalizeColor(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = strtolower(trim($value));

        $map = [
            'black & tan' => 'black_and_tan',
            'black and tan' => 'black_and_tan',
            'black tan' => 'black_and_tan',
            'blk/tan' => 'black_and_tan',
            'b&t' => 'black_and_tan',
            'fekete-cser' => 'black_and_tan',

            'white' => 'white',
            'wht' => 'white',
        ];

        foreach ($map as $pattern => $normalized) {
            if (str_contains($value, $pattern)) {
                return $normalized;
            }
        }

        return preg_replace('/[^a-z0-9]+/', '_', $value);
    }

    private function normalizeCountry(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = strtoupper(trim($value));

        $map = [
            'UK' => 'GB',
            'U.K.' => 'GB',
            'UNITED KINGDOM' => 'GB',
            'GREAT BRITAIN' => 'GB',
            'BRITAIN' => 'GB',
            'GB' => 'GB',

            'USA' => 'US',
            'U.S.' => 'US',
            'UNITED STATES' => 'US',
            'AMERICA' => 'US',
            'US' => 'US',
        ];

        return $map[$value] ?? $value;
    }

    private function normalizeName(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value;
    }
}