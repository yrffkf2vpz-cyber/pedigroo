<?php

namespace App\Services\Normalizers\RegNo;

use App\Services\Normalizers\Support\FuzzyMatchService;

class RegNoCountryDetector
{
    public function __construct(
        protected RegNoNormalizer    $normalizer,
        protected FuzzyMatchService  $fuzzy
    ) {}

    /**
     * Teljes országfelismerés regisztrációs számból:
     * - prefix alapján
     * - issuer alapján
     * - szám + év alapján
     * - fuzzy fallback
     * - canonical country name
     */
    public function detect(?string $rawRegNo): ?string
    {
        if (!$rawRegNo) {
            return null;
        }

        $raw = trim($rawRegNo);

        // ---------------------------------------------------------
        // 1) Normalizálás (prefix, number, year)
        // ---------------------------------------------------------
        $normalized = $this->normalizer->normalize($raw);

        $prefix = $normalized['prefix'];
        $number = $normalized['number'];
        $year   = $normalized['year'];

        // ---------------------------------------------------------
        // 2) Prefix alapján ország felismerése
        // ---------------------------------------------------------
        if ($prefix) {
            $country = $this->detectFromPrefix($prefix);
            if ($country) {
                return $country;
            }
        }

        // ---------------------------------------------------------
        // 3) Issuer alapján ország felismerése
        // ---------------------------------------------------------
        $issuer = $this->detectIssuer($raw);
        if ($issuer) {
            $country = $this->issuerToCountry($issuer);
            if ($country) {
                return $country;
            }
        }

        // ---------------------------------------------------------
        // 4) Szám + év alapján ország felismerése
        // ---------------------------------------------------------
        if ($number && $year) {
            $country = $this->detectFromNumberYear($number, $year);
            if ($country) {
                return $country;
            }
        }

        // ---------------------------------------------------------
        // 5) Fuzzy fallback
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchRegNo($raw);
        if ($fuzzy && !empty($fuzzy['country'])) {
            return $fuzzy['country'];
        }

        // ---------------------------------------------------------
        // 6) Legacy regex fallback
        // ---------------------------------------------------------
        return $this->detectFromLegacyRules($raw);
    }

    /**
     * Prefix → ország felismerés.
     */
    private function detectFromPrefix(string $prefix): ?string
    {
        $prefix = strtoupper($prefix);

        $map = [
            // USA
            'AKC'  => 'United States',
            'UKC'  => 'United States',

            // Canada
            'CKC'  => 'Canada',

            // United Kingdom
            'KC'   => 'United Kingdom',

            // Australia
            'ANKC' => 'Australia',

            // New Zealand
            'NZKC' => 'New Zealand',

            // International
            'FCI'  => 'International',

            // Hungary
            'MEOE' => 'Hungary',
            'MET'  => 'Hungary',

            // Czech Republic
            'CMKU' => 'Czech Republic',

            // Slovakia
            'SKJ'  => 'Slovakia',

            // Norway
            'NKK'  => 'Norway',

            // Denmark
            'DKK'  => 'Denmark',

            // Germany
            'VDH'  => 'Germany',
            'SV'   => 'Germany',

            // Austria
            'ÖKV'  => 'Austria',
            'ÖHZB' => 'Austria',

            // France
            'LOF'  => 'France',

            // Italy
            'ROI'  => 'Italy',

            // Romania
            'RSR'  => 'Romania',
        ];

        return $map[$prefix] ?? null;
    }

    /**
     * Issuer → ország.
     */
    private function issuerToCountry(string $issuer): ?string
    {
        $issuer = strtoupper($issuer);

        $map = [
            'AKC'  => 'United States',
            'CKC'  => 'Canada',
            'KC'   => 'United Kingdom',
            'ANKC' => 'Australia',
            'NZKC' => 'New Zealand',
            'MEOE' => 'Hungary',
            'MET'  => 'Hungary',
            'CMKU' => 'Czech Republic',
            'SKJ'  => 'Slovakia',
            'NKK'  => 'Norway',
            'DKK'  => 'Denmark',
            'VDH'  => 'Germany',
            'SV'   => 'Germany',
            'ÖKV'  => 'Austria',
            'LOF'  => 'France',
            'ROI'  => 'Italy',
            'RSR'  => 'Romania',
        ];

        return $map[$issuer] ?? null;
    }

    /**
     * Issuer felismerése regno_rules.php alapján.
     */
    private function detectIssuer(string $raw): ?string
    {
        $rules = config('regno_rules', []);

        foreach ($rules as $country => $data) {
            if (empty($data['patterns'])) {
                continue;
            }

            foreach ($data['patterns'] as $pattern => $issuer) {
                if (@preg_match($pattern, $raw)) {
                    return $issuer;
                }
            }
        }

        return null;
    }

    /**
     * Szám + év alapján ország felismerése.
     */
    private function detectFromNumberYear(string $number, int $year): ?string
    {
        $rules = config('regno_rules', []);

        foreach ($rules as $country => $data) {
            if (empty($data['number_year_ranges'])) {
                continue;
            }

            foreach ($data['number_year_ranges'] as $range) {
                if (
                    $year   >= ($range['year_start'] ?? 0) &&
                    $year   <= ($range['year_end']   ?? 9999) &&
                    $number >= ($range['num_start']  ?? 0) &&
                    $number <= ($range['num_end']    ?? 999999)
                ) {
                    return $country;
                }
            }
        }

        return null;
    }

    /**
     * Legacy regex fallback.
     */
    private function detectFromLegacyRules(string $raw): ?string
    {
        $rules = config('regno_rules', []);

        foreach ($rules as $country => $data) {
            if (empty($data['patterns'])) {
                continue;
            }

            foreach ($data['patterns'] as $pattern => $issuer) {
                if (@preg_match($pattern, $raw)) {
                    return $country;
                }
            }
        }

        return null;
    }
}