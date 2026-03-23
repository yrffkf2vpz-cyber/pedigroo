<?php

namespace App\Services\Normalizers\RegNo;

use App\Services\Normalizers\Support\FuzzyMatchService;

class RegNoParser
{
    public function __construct(
        protected RegNoNormalizer       $normalizer,
        protected RegNoCountryDetector $countryDetector,
        protected FuzzyMatchService    $fuzzy
    ) {}

    /**
     * Teljes regisztrációs szám feldolgozása:
     * - prefix felismerés
     * - szám normalizálás
     * - év felismerés
     * - issuer felismerés
     * - ország felismerés
     * - fuzzy fallback
     * - canonical form
     */
    public function parse(?string $raw): array
    {
        if (!$raw) {
            return $this->emptyResult();
        }

        $raw = trim($raw);

        // ---------------------------------------------------------
        // 1) Normalizálás (prefix, number, year, clean)
        // ---------------------------------------------------------
        $normalized = $this->normalizer->normalize($raw);

        // ---------------------------------------------------------
        // 2) Ország felismerése
        // ---------------------------------------------------------
        $country = $this->countryDetector->detect($raw);

        // ---------------------------------------------------------
        // 3) Issuer felismerése (regno_rules.php alapján)
        // ---------------------------------------------------------
        $issuer = $this->detectIssuer($raw);

        // ---------------------------------------------------------
        // 4) Ha nincs issuer vagy country → fuzzy fallback
        // ---------------------------------------------------------
        if (!$issuer || !$country) {
            $fuzzy = $this->fuzzy->matchRegNo($raw);

            if ($fuzzy) {
                $issuer  = $issuer  ?: $fuzzy['issuer'];
                $country = $country ?: $fuzzy['country'];

                // prefix fallback
                if (!$normalized['prefix'] && !empty($fuzzy['prefix'])) {
                    $normalized['prefix'] = $fuzzy['prefix'];
                }
            }
        }

        // ---------------------------------------------------------
        // 5) Canonical clean regisztrációs szám
        // ---------------------------------------------------------
        $clean = $this->canonicalClean(
            prefix: $normalized['prefix'],
            number: $normalized['number'],
            year:   $normalized['year']
        );

        return [
            'raw'      => $raw,
            'prefix'   => $normalized['prefix'],
            'number'   => $normalized['number'],
            'year'     => $normalized['year'],
            'country'  => $country,
            'issuer'   => $issuer,
            'clean'    => $clean,
        ];
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
     * Canonical clean regisztrációs szám:
     *   PREFIX-NUMBER-YEAR
     *   pl. MET-12345-18
     *   pl. AKC-DN123456
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

    private function emptyResult(): array
    {
        return [
            'raw'      => null,
            'prefix'   => null,
            'number'   => null,
            'year'     => null,
            'country'  => null,
            'issuer'   => null,
            'clean'    => null,
        ];
    }
}