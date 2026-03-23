<?php

declare(strict_types=1);

namespace App\Services\Normalizers;

use App\Services\Normalizers\Color\Support\ColorFuzzyMatcher;
use App\Services\Normalizers\Color\Maps\GlobalSimpleColors;
use App\Services\Normalizers\Color\Maps\GlobalComplexColors;
use Illuminate\Support\Facades\DB;

class ColorNormalizer
{
    public static function normalize(?string $input, ?int $breedId = null): ?string
    {
        if (!$input) {
            return null;
        }

        $clean = self::clean($input);

        // 1) fajtaspecifikus
        if ($breedId) {
            if ($breedMatch = self::matchBreedSpecific($clean, $breedId)) {
                return $breedMatch;
            }
        }

        // 2) komplex globális színek
        if ($complex = ColorFuzzyMatcher::matchMap($clean, GlobalComplexColors::map())) {
            return $complex;
        }

        // 3) egyszerű globális színek
        if ($simple = ColorFuzzyMatcher::matchMap($clean, GlobalSimpleColors::map())) {
            return $simple;
        }

        // 4) kombinációk
        if ($combo = self::matchCombination($clean)) {
            return $combo;
        }

        // 5) fallback – megtartjuk az eredetit
        return strtoupper($clean);
    }

    private static function clean(string $input): string
    {
        $s = strtolower(trim($input));
        $s = str_replace(['/', '\\', '-', '_'], ' ', $s);
        $s = preg_replace('/\s+/', ' ', $s);
        return trim($s);
    }

    private static function matchBreedSpecific(string $clean, int $breedId): ?string
    {
        $colors = DB::table('pd_breed_colors')
            ->where('breed_id', $breedId)
            ->pluck('color_name')
            ->toArray();

        foreach ($colors as $color) {
            if (ColorFuzzyMatcher::matchTo($clean, $color)) {
                return strtoupper($color);
            }
        }

        return null;
    }

    private static function matchCombination(string $clean): ?string
    {
        $parts = explode(' ', $clean);

        $normalized = [];

        foreach ($parts as $p) {
            $simple = ColorFuzzyMatcher::matchMap($p, GlobalSimpleColors::map());
            if ($simple) {
                $normalized[] = $simple;
            }
        }

        if (count($normalized) >= 2) {
            return implode(' AND ', array_unique($normalized));
        }

        return null;
    }
}