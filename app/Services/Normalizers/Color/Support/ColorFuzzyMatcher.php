<?php

namespace App\Services\Normalizers\Color\Support;

use App\Services\Normalize\Color\Master\GlobalColorMap;

class ColorFuzzyMatcher
{
    public static function match(?string $raw): ?string
    {
        if (!$raw) return null;

        $raw = mb_strtolower(trim($raw), 'UTF-8');

        foreach (GlobalColorMap::map() as $normalized => $variants) {
            foreach ($variants as $variant) {
                if ($raw === $variant) {
                    return $normalized;
                }
                if (str_contains($raw, $variant)) {
                    return $normalized;
                }
            }
        }

        return null;
    }
}