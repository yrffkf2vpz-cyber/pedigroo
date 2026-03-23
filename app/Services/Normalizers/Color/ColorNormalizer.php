<?php

namespace App\Services\Normalizers\Color;

use App\Services\Normalize\Color\Contracts\ColorNormalizerInterface;
use App\Services\Normalize\Color\Master\BreedColorMap;
use App\Services\Normalize\Color\Support\ColorFuzzyMatcher;

class ColorNormalizer implements ColorNormalizerInterface
{
    public function normalize(?string $rawColor, ?string $breedCode = null): ?string
    {
        if (!$rawColor) return null;

        // 1) globális fuzzy match
        $normalized = ColorFuzzyMatcher::match($rawColor);
        if (!$normalized) {
            return null; // késobb: "unknown_color" vagy log
        }

        // 2) fajtaspecifikus szurés (ha van breed)
        if ($breedCode) {
            $breedColors = $this->getBreedColors($breedCode);

            if (!empty($breedColors) && !in_array($normalized, $breedColors, true)) {
                // fajtaszabály szerint nem engedélyezett szín
                // késobb: warning, log, suggestion
                return $normalized; // most még visszaadjuk, csak megjegyezzük
            }
        }

        return $normalized;
    }

    private function getBreedColors(string $breedCode): array
    {
        $map = BreedColorMap::map();

        $key = mb_strtolower($breedCode, 'UTF-8');

        return $map[$key] ?? [];
    }
}