<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class QualificationPromotionNormalizer
{
    /**
     * Normalize and promote a qualification string into pd_qualifications.
     * Returns final pd_qualifications.id
     */
    public static function promote(?string $raw): ?int
    {
        if (!$raw) {
            return null;
        }

        $clean = self::clean($raw);
        $code  = self::mapToCode($clean);

        // 1) Check if qualification already exists
        $existing = DB::table('pd_qualifications')
            ->where('code', $code)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Insert new qualification
        return DB::table('pd_qualifications')->insertGetId([
            'code'       => $code,
            'label'      => ucfirst(strtolower($code)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /* ---------------------------------------------------------
     *  CLEANER
     * --------------------------------------------------------- */

    protected static function clean(string $value): string
    {
        $value = strtoupper(trim($value));
        $value = preg_replace('/[^A-Z0-9]/', '', $value); // remove dots, spaces, hyphens
        return $value;
    }

    /* ---------------------------------------------------------
     *  MAPPING
     * --------------------------------------------------------- */

    protected static function mapToCode(string $clean): string
    {
        $map = [
            // CAC family
            'CAC'       => 'CAC',
            'CACJ'      => 'CACJ',
            'RCAC'      => 'RESCAC',
            'RESCAC'    => 'RESCAC',

            // CACIB family
            'CACIB'     => 'CACIB',
            'RCACIB'    => 'RESCACIB',
            'RESCACIB'  => 'RESCACIB',

            // Hungarian junior
            'HPJ'       => 'HPJ',

            // Quality grades
            'EXC'       => 'EXCELLENT',
            'EXCELLENT' => 'EXCELLENT',
            'VG'        => 'VERY_GOOD',
            'VERYGOOD'  => 'VERY_GOOD',
            'G'         => 'GOOD',
            'GOOD'      => 'GOOD',
        ];

        return $map[$clean] ?? $clean; // fallback: use cleaned code
    }
}