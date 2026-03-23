<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class KennelNameDetector
{
    /**
     * Detect kennel name based on repetition in database.
     * If the first token appears as first token in >= 2 dogs → kennel name.
     */
    public static function detect(string $fullName): array
    {
        $tokens = explode(' ', trim($fullName));
        if (count($tokens) < 2) {
            return [null, $fullName];
        }

        $first = mb_strtolower($tokens[0]);

        // Count how many dogs start with this token
        $count = DB::table('pedroo_dogs')
            ->whereRaw("LOWER(real_name) LIKE ?", [$first . '%'])
            ->count();

        if ($count >= 2) {
            // Kennel name detected
            $kennel = array_shift($tokens);
            $call   = implode(' ', $tokens);
            return [$kennel, $call];
        }

        // No kennel name detected
        return [null, $fullName];
    }
}