<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class RingPromotionNormalizer
{
    /**
     * Normalize and promote ring into pd_rings.
     * Returns final pd_rings.id
     */
    public static function promote(?string $raw): ?int
    {
        if (!$raw) {
            return null;
        }

        $clean = self::clean($raw);
        if (!$clean) {
            return null;
        }

        // 1) Check if ring already exists
        $existing = DB::table('pd_rings')
            ->where('number', $clean)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Insert new ring
        return DB::table('pd_rings')->insertGetId([
            'number'     => $clean,
            'label'      => 'Ring ' . $clean,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /* ---------------------------------------------------------
     *  CLEANER
     * --------------------------------------------------------- */
    protected static function clean(string $value): ?string
    {
    $value = strtoupper(trim($value));

    // Remove prefixes (international + Hungarian + Romanian + Portuguese)
    $value = str_replace(
        [
            // Romanian
            'RINGUL', 'RINGUL:', 'RINGUL NR', 'RINGUL NR.', 
            'RING NR', 'RING NR.',

            // Portuguese
            'RINGUE', 'RINGUE:', 'RINGUE NR', 'RINGUE NR.',

            // International
            'RING', 'RING:', 'R ', 'R',

            // Hungarian
            'KÖR', 'KOR', 'KÖR:', 'KOR:'
        ],
        '',
        $value
    );

    $value = trim($value);

    // Roman → arab
    $roman = [
        'I'    => '1',
        'II'   => '2',
        'III'  => '3',
        'IV'   => '4',
        'V'    => '5',
        'VI'   => '6',
        'VII'  => '7',
        'VIII' => '8',
        'IX'   => '9',
        'X'    => '10',
    ];

    if (isset($roman[$value])) {
        return $roman[$value];
    }

    // Accept single letters (Portuguese ring A/B/C)
    if (preg_match('/^[A-Z]$/', $value)) {
        return $value;
    }

    // Remove leading zeros
    $value = ltrim($value, '0');

    return $value ?: null;
    }
}