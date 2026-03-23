<?php

namespace App\Services\Normalizers\RegNo;

class CountryPatterns
{
    public static function detectCountry(?string $regNo): ?string
    {
        if (!$regNo) return null;

        $r = strtoupper($regNo);

        // HU – MET 12345/2020
        if (str_contains($r, 'MET')) {
            return 'HU';
        }

        // UK – KC AT01234506, KC AA01234501
        if (str_starts_with($r, 'KC ') || preg_match('/\bAT\d{6}\d{2}\b/i', $r)) {
            return 'UK';
        }

        // US – AKC DN12345607
        if (str_starts_with($r, 'AKC ') || preg_match('/\bDN\d{8}\b/i', $r)) {
            return 'US';
        }

        // FR – LOF 123456/01234
        if (str_contains($r, 'LOF')) {
            return 'FR';
        }

        // DE – VDH 123456
        if (str_contains($r, 'VDH')) {
            return 'DE';
        }

        return null;
    }
}