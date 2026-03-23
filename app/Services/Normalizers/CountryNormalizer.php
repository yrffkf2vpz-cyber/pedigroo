<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class CountryNormalizer
{
    /**
     * Get or create country_id from country name.
     */
    public static function id(?string $country): ?int
    {
        if (!$country) {
            return null;
        }

        $clean = self::clean($country);

        // Try to find existing country
        $existing = DB::table("countries")
            ->where("normalized_name", $clean)
            ->value("id");

        if ($existing) {
            return $existing;
        }

        // Create new country
        return DB::table("countries")->insertGetId([
            "name"            => $country,
            "normalized_name" => $clean,
            "created_at"      => now(),
            "updated_at"      => now()
        ]);
    }

    /**
     * Normalize country names for matching.
     */
    protected static function clean(string $value): string
    {
        $value = trim($value);
        $value = mb_strtolower($value);

        // Replace accents (optional but useful)
        $value = str_replace(
            ['á','é','í','ó','ö','ő','ú','ü','ű'],
            ['a','e','i','o','o','o','u','u','u'],
            $value
        );

        // Remove punctuation
        $value = str_replace([",", ".", "(", ")", "-"], " ", $value);

        // Collapse multiple spaces
        $value = preg_replace("/\s+/", " ", $value);

        return $value;
    }
}