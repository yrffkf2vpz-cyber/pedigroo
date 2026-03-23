<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class RingNormalizer
{
    /**
     * Normalize a ring name and return sandbox ring_id.
     */
    public static function id(?string $name): ?int
    {
        if (!$name || trim($name) === '') {
            return null;
        }

        $clean = self::clean($name);
        $hash  = sha1($clean);

        // 1) Try to find existing ring in sandbox
        $existing = DB::table('pedroo_rings')
            ->where('hash', $hash)
            ->value('id');

        if ($existing) {
            return $existing;
        }

        // 2) Insert new ring into sandbox
        return DB::table('pedroo_rings')->insertGetId([
            'source'      => 'parser',
            'external_id' => null,
            'name'        => $clean,
            'raw'         => $name,
            'hash'        => $hash,
            'confidence'  => 90,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /**
     * Clean ring name for matching.
     */
    protected static function clean(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);

        // Normalize common formats
        $name = str_ireplace(['ring', 'ring:'], '', $name);
        $name = trim($name);

        return ucfirst($name);
    }
}