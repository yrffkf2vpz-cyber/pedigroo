<?php

namespace App\Services\Normalizers\RegNo;

use App\Services\Normalizers\Support\FuzzyMatchService;

class RegNoNormalizer
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes regisztrációs szám normalizálás:
     * - prefix felismerés
     * - szám normalizálás
     * - év felismerés
     * - canonical form
     * - fuzzy fallback
     */
    public function normalize(?string $raw): array
    {
        if (!$raw) {
            return $this->empty();
        }

        $raw = trim($raw);
        $upper = strtoupper($raw);

        // ---------------------------------------------------------
        // 1) ORSZÁG-SPECIFIKUS FORMÁTUMOK
        // ---------------------------------------------------------
        $countrySpecific = $this->matchCountrySpecific($upper);
        if ($countrySpecific) {
            return $countrySpecific;
        }

        // ---------------------------------------------------------
        // 2) GENERIC PREFIX + NUMBER + YEAR
        // ---------------------------------------------------------
        $generic = $this->matchGeneric($upper);
        if ($generic) {
            return $generic;
        }

        // ---------------------------------------------------------
        // 3) FUZZY FALLBACK
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchRegNo($raw);
        if ($fuzzy) {
            return [
                'raw'        => $raw,
                'prefix'     => $fuzzy['prefix'] ?? null,
                'number'     => $fuzzy['number'] ?? null,
                'year'       => $fuzzy['year'] ?? null,
                'clean'      => $this->canonicalClean(
                    $fuzzy['prefix'] ?? null,
                    $fuzzy['number'] ?? null,
                    $fuzzy['year'] ?? null
                ),
                'confidence' => 0.60,
            ];
        }

        // ---------------------------------------------------------
        // 4) SEMMI NEM ILLIK ? RAW NORMALIZÁLÁS
        // ---------------------------------------------------------
        return [
            'raw'        => $raw,
            'prefix'     => null,
            'number'     => null,
            'year'       => null,
            'clean'      => null,
            'confidence' => 0.10,
        ];
    }

    /**
     * Ország-specifikus formátumok felismerése.
     */
    private function matchCountrySpecific(string $r): ?array
    {
        // ---------------------------------------------------------
        // HUNGARY – MET 12345/2020
        // ---------------------------------------------------------
        if (preg_match('/\bMET\s+(\d{1,6})\/(\d{2,4})\b/', $r, $m)) {
            return $this->build('MET', $m[1], $this->normalizeYear($m[2]), 'HU', 0.95);
        }

        // ---------------------------------------------------------
        // USA – AKC DN12345607
        // ---------------------------------------------------------
        if (preg_match('/\bAKC\s*([A-Z]{2}\d{6,8})\b/', $r, $m)) {
            return $this->build('AKC', $m[1], null, 'US', 0.90);
        }

        // ---------------------------------------------------------
        // UK – KC AT01234506
        // ---------------------------------------------------------
        if (preg_match('/\bKC\s*([A-Z]{2}\d{6,8})\b/', $r, $m)) {
            return $this->build('KC', $m[1], null, 'UK', 0.90);
        }

        // ---------------------------------------------------------
        // FRANCE – LOF 123456/01234
        // ---------------------------------------------------------
        if (preg_match('/\bLOF\s+(\d{1,6})\/(\d{1,5})\b/', $r, $m)) {
            return $this->build('LOF', $m[1], null, 'FR', 0.90);
        }

        // ---------------------------------------------------------
        // GERMANY – VDH 123456
        // ---------------------------------------------------------
        if (preg_match('/\bVDH\s+(\d{4,8})\b/', $r, $m)) {
            return $this->build('VDH', $m[1], null, 'DE', 0.85);
        }

        // ---------------------------------------------------------
        // AUSTRIA – ÖHZB 12345
        // ---------------------------------------------------------
        if (preg_match('/\bÖHZB\s+(\d{3,8})\b/u', $r, $m)) {
            return $this->build('ÖHZB', $m[1], null, 'AT', 0.85);
        }

        // ---------------------------------------------------------
        // CZECH REPUBLIC – CMKU/ABC/12345/20
        // ---------------------------------------------------------
        if (preg_match('/\bCMKU\/[A-Z]{2,4}\/(\d{1,6})\/(\d{2,4})\b/', $r, $m)) {
            return $this->build('CMKU', $m[1], $this->normalizeYear($m[2]), 'CZ', 0.90);
        }

        // ---------------------------------------------------------
        // SLOVAKIA – SKJ 12345/20
        // ---------------------------------------------------------
        if (preg_match('/\bSKJ\s+(\d{1,6})\/(\d{2,4})\b/', $r, $m)) {
            return $this->build('SKJ', $m[1], $this->normalizeYear($m[2]), 'SK', 0.90);
        }

        return null;
    }

    /**
     * Generikus prefix + szám + év felismerése.
     */
    private function matchGeneric(string $r): ?array
    {
        // PREFIX 12345/20
        if (preg_match('/\b([A-Z]{2,5})\s+(\d{1,8})\/(\d{2,4})\b/', $r, $m)) {
            return $this->build(
                $m[1],
                $m[2],
                $this->normalizeYear($m[3]),
                null,
                0.70
            );
        }

        // PREFIX 12345
        if (preg_match('/\b([A-Z]{2,5})\s+(\d{1,8})\b/', $r, $m)) {
            return $this->build($m[1], $m[2], null, null, 0.60);
        }

        return null;
    }

    /**
     * Canonical clean regisztrációs szám.
     */
    private function canonicalClean(?string $prefix, ?string $number, ?string $year): ?string
    {
        if (!$prefix && !$number) {
            return null;
        }

        if ($year) {
            return strtoupper("{$prefix}-{$number}-{$year}");
        }

        return strtoupper("{$prefix}-{$number}");
    }

    /**
     * Év normalizálása (pl. 20 ? 2020).
     */
    private function normalizeYear(string $year): int
    {
        if (strlen($year) === 2) {
            $y = intval($year);
            return $y >= 80 ? 1900 + $y : 2000 + $y;
        }

        return intval($year);
    }

    /**
     * Normalizált struktúra építése.
     */
    private function build(string $prefix, string $number, ?int $year, ?string $country, float $confidence): array
    {
        return [
            'raw'        => null,
            'prefix'     => strtoupper($prefix),
            'number'     => $number,
            'year'       => $year,
            'country'    => $country,
            'clean'      => $this->canonicalClean($prefix, $number, $year),
            'confidence' => $confidence,
        ];
    }

    private function empty(): array
    {
        return [
            'raw'        => null,
            'prefix'     => null,
            'number'     => null,
            'year'       => null,
            'country'    => null,
            'clean'      => null,
            'confidence' => 0.0,
        ];
    }
}