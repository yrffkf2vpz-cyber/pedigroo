<?php

namespace App\Services\Normalizers;

use Illuminate\Support\Facades\DB;

class PlacementPromotionNormalizer
{
    /**
     * Normalize and promote placement into pd_placements.
     * Returns final pd_placements.id
     */
    public static function promote(?string $raw): ?int
    {
        if (!$raw) {
            return null;
        }

        $clean = self::clean($raw);
        $code  = self::mapToCode($clean);

        if (!$code) {
            return null;
        }

        // 1) Check if placement already exists
        $existing = DB::table('pd_placements')
            ->where('code', $code)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 2) Insert new placement
        return DB::table('pd_placements')->insertGetId([
            'code'       => $code,
            'label'      => ucfirst(strtolower(str_replace('_', ' ', $code))),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /* ---------------- CLEANER ---------------- */

    protected static function clean(string $value): string
    {
        $value = trim($value);
        $value = strtoupper($value);
        $value = str_replace(['.', ','], '', $value);
        $value = preg_replace('/\s+/', ' ', $value);
        return $value;
    }

    /* ---------------- MAPPING ---------------- */

    protected static function mapToCode(string $clean): ?string
    {
        // Roman → arab
        $roman = [
            'I'   => '1',
            'II'  => '2',
            'III' => '3',
            'IV'  => '4',
        ];

        if (isset($roman[$clean])) {
            $clean = $roman[$clean];
        }

        // Pure numeric placements
        if (in_array($clean, ['1', '1ST', 'FIRST', 'WINNER'])) {
            return 'PLACE_1';
        }
        if (in_array($clean, ['2', '2ND', 'SECOND'])) {
            return 'PLACE_2';
        }
        if (in_array($clean, ['3', '3RD', 'THIRD'])) {
            return 'PLACE_3';
        }
        if (in_array($clean, ['4', '4TH', 'FOURTH'])) {
            return 'PLACE_4';
        }

        // BOB / BOS / specials
        $map = [
            'BOB'               => 'BOB',
            'BOS'               => 'BOS',
            'BEST OF BREED'     => 'BOB',
            'BEST OPPOSITE SEX' => 'BOS',
            'BEST MALE'         => 'BEST_MALE',
            'BEST FEMALE'       => 'BEST_FEMALE',
        ];

        if (isset($map[$clean])) {
            return $map[$clean];
        }

        return null;
    }
}