<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class DogPromotionNormalizer
{
    /**
     * Promote dog_name from sandbox into pd_dogs.
     * Returns final pd_dogs.id
     */
    public static function promoteFromName(string $name): int
    {
        $clean = self::cleanName($name);

        // 1) Check if dog already exists in final table
        $existing = DB::table('pd_dogs')
            ->where('name', $clean)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Create minimal dog record
        return DB::table('pd_dogs')->insertGetId([
            'name'       => $clean,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /* ---------------------------------------------------------
     *  CLEANER
     * --------------------------------------------------------- */

    protected static function cleanName(?string $name): string
    {
        if (!$name) {
            return 'Unknown Dog';
        }

        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);

        return ucfirst($name);
    }
}