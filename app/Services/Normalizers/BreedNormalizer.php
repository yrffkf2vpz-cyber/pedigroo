<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class BreedNormalizer
{
    /**
     * Get or create breed_id from breed name.
     */
    public static function id(?string $breed): ?int
    {
        if (!$breed) {
            return null;
        }

        // 1) Clean input
        $clean = self::clean($breed);

        // 2) Try config-based aliases first
        $config = config('breed_rules', []);

        foreach ($config as $breedId => $info) {
            // name match
            if (isset($info['name']) && self::clean($info['name']) === $clean) {
                return $breedId;
            }

            // alias match
            if (!empty($info['aliases']) && is_array($info['aliases'])) {
                foreach ($info['aliases'] as $alias) {
                    if (self::clean($alias) === $clean) {
                        return $breedId;
                    }
                }
            }
        }

        // 3) Try to find existing breed in DB
        $existing = DB::table("breeds")
            ->where("normalized_name", $clean)
            ->value("id");

        if ($existing) {
            return $existing;
        }

        // 4) Create new breed
        return DB::table("breeds")->insertGetId([
            "name"            => $breed,
            "normalized_name" => $clean,
            "created_at"      => now(),
            "updated_at"      => now()
        ]);
    }

    /**
     * Normalize breed names for matching.
     */
    protected static function clean(string $value): string
    {
        $value = trim($value);
        $value = mb_strtolower($value);

        // Replace accents
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