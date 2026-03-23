<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class JudgePromotionNormalizer
{
    /**
     * Normalize and promote judge name into pd_judges.
     * Returns final pd_judges.id
     */
    public static function promote(?string $raw): ?int
    {
        if (!$raw) {
            return null;
        }

        $clean = self::cleanName($raw);
        [$last, $first] = self::splitName($clean);

        // 1) Try to find existing judge
        $existing = DB::table('pd_judges')
            ->where('full_name', $clean)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Insert new judge
        return DB::table('pd_judges')->insertGetId([
            'full_name'  => $clean,
            'last_name'  => $last,
            'first_name' => $first,
            'country'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /* ---------------------------------------------------------
     *  CLEANER
     * --------------------------------------------------------- */

    protected static function cleanName(string $name): string
    {
        // Remove titles
        $name = preg_replace('/\b(Dr|Prof|Mr|Mrs|Ms|Miss)\.?/i', '', $name);

        // Remove commas
        $name = str_replace(',', ' ', $name);

        // Normalize whitespace
        $name = preg_replace('/\s+/', ' ', trim($name));

        // Uppercase first letters
        return mb_convert_case($name, MB_CASE_TITLE, "UTF-8");
    }

    /* ---------------------------------------------------------
     *  NAME SPLITTER
     * --------------------------------------------------------- */

    protected static function splitName(string $name): array
    {
        $parts = explode(' ', $name);

        if (count($parts) === 1) {
            return [$parts[0], null];
        }

        // Assume last name first (common in Europe)
        $last  = $parts[0];
        $first = implode(' ', array_slice($parts, 1));

        return [$last, $first];
    }
}