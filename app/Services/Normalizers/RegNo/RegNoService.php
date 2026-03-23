<?php

namespace App\Services\Normalizers\RegNo;

use App\Services\Normalizers\Support\FuzzyMatchService;

class RegNoService
{
    public function __construct(
        protected RegNoNormalizer       $normalizer,
        protected RegNoCountryDetector $countryDetector,
        protected RegNoParser          $parser,
        protected FuzzyMatchService    $fuzzy
    ) {}

    /**
     * TELJES 3.0‑ÁS REGISZTRÁCIÓS SZÁM NORMALIZÁLÁS
     */
    public function process(?string $rawRegNo, ?string $rawCountry = null, bool $debug = false): array
    {
        if (!$rawRegNo) {
            return $this->emptyResult($debug);
        }

        $raw = trim($rawRegNo);

        // ---------------------------------------------------------
        // 1) NORMALIZÁLÁS (prefix + number + year)
        // ---------------------------------------------------------
        $normalized = $this->normalizer->normalize($raw);

        // ---------------------------------------------------------
        // 2) ORSZÁG FELISMERÉS (regno alapján)
        // ---------------------------------------------------------
        $country = $this->countryDetector->detect($raw) ?: $rawCountry;

        // ---------------------------------------------------------
        // 3) PARSE (issuer + clean)
        // ---------------------------------------------------------
        $parsed = $this->parser->parse($raw);

        // ---------------------------------------------------------
        // 4) FUZZY FALLBACK
        // ---------------------------------------------------------
        $fuzzy = null;

        if (!$parsed['issuer'] || !$country || !$normalized['prefix']) {
            $fuzzy = $this->fuzzy->matchRegNo($raw);

            if ($fuzzy) {
                $parsed['issuer']       = $parsed['issuer']       ?: $fuzzy['issuer'];
                $country                = $country                ?: $fuzzy['country'];
                $normalized['prefix']   = $normalized['prefix']   ?: $fuzzy['prefix'];
                $normalized['registry'] = $normalized['registry'] ?: $fuzzy['registry'];
            }
        }

        // ---------------------------------------------------------
        // 5) ERA CLASSIFICATION (ancient / classic / modern)
        // ---------------------------------------------------------
        $classification = $this->classifyEra($normalized['year']);

        // ---------------------------------------------------------
        // 6) CANONICAL OUTPUT
        // ---------------------------------------------------------
        $result = [
            'raw'            => $raw,
            'clean'          => $parsed['clean'],
            'prefix'         => $normalized['prefix'],
            'registry'       => $normalized['registry'] ?? null,
            'number'         => $normalized['number'],
            'year'           => $normalized['year'],
            'issuer'         => $parsed['issuer'],
            'country'        => $country,
            'classification' => $classification,
        ];

        if ($debug) {
            $result['debug'] = [
                'raw' => $raw,

                'normalizer' => [
                    'prefix'   => $normalized['prefix'],
                    'registry' => $normalized['registry'] ?? null,
                    'number'   => $normalized['number'],
                    'year'     => $normalized['year'],
                ],

                'country_detection' => [
                    'detected_country' => $country,
                ],

                'parser' => [
                    'issuer' => $parsed['issuer'],
                    'clean'  => $parsed['clean'],
                ],

                'classification' => $classification,
                'fuzzy'          => $fuzzy,
            ];
        }

        return $result;
    }

    // ---------------------------------------------------------
    // ERA CLASSIFICATION
    // ---------------------------------------------------------

    private function classifyEra(?int $year): string
    {
        if (!$year) {
            return 'modern';
        }

        return match (true) {
            $year < 1980 => 'ancient',
            $year < 2000 => 'classic',
            default      => 'modern',
        };
    }

    // ---------------------------------------------------------
    // EMPTY RESULT
    // ---------------------------------------------------------

    private function emptyResult(bool $debug): array
    {
        $base = [
            'raw'            => null,
            'clean'          => null,
            'prefix'         => null,
            'registry'       => null,
            'number'         => null,
            'year'           => null,
            'issuer'         => null,
            'country'        => null,
            'classification' => 'modern',
        ];

        if ($debug) {
            $base['debug'] = [
                'raw'                => null,
                'normalizer'         => null,
                'country_detection'  => null,
                'parser'             => null,
                'classification'     => 'modern',
                'fuzzy'              => null,
            ];
        }

        return $base;
    }
}