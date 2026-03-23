<?php

namespace App\Services\Normalizers;

use App\Models\PdCountry;
use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeCountryService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes ország-normalizálás (globális, ISO, kennelklub prefix).
     */
    public function normalize(?string $rawCountry, ?string $rawRegNo = null, bool $debug = false): array
    {
        $input = $rawCountry ? trim($rawCountry) : null;

        // ---------------------------------------------------------
        // 1) REGISZTRÁCIÓS PREFIX → ORSZÁG FELISMERÉS
        // ---------------------------------------------------------
        if ($rawRegNo) {
            if ($prefixCountry = $this->detectFromRegNo($rawRegNo)) {
                return $this->finalize($prefixCountry, $input, 'regno_prefix', $debug);
            }
        }

        if (!$input) {
            return $this->result(null, null, null, 'empty_input', $input, [], $debug);
        }

        // ---------------------------------------------------------
        // 2) NORMALIZÁLT SZÖVEG
        // ---------------------------------------------------------
        $normalized = $this->normalizeText($input);

        // ---------------------------------------------------------
        // 3) TÖBBNYELVŰ ALIASOK
        // ---------------------------------------------------------
        $alias = $this->aliasMap();
        if (isset($alias[$normalized])) {
            return $this->finalize($alias[$normalized], $input, 'alias', $debug);
        }

        // ---------------------------------------------------------
        // 4) ISO KÓDOK (2 és 3 betűs)
        // ---------------------------------------------------------
        $iso = $this->isoMap();
        if (isset($iso[$normalized])) {
            return $this->finalize($iso[$normalized], $input, 'iso_code', $debug);
        }

        // ---------------------------------------------------------
        // 5) ORSZÁG-SPECIFIKUS ÁTNEVEZÉSEK
        // ---------------------------------------------------------
        $countryMap = $this->countrySpecificMap();
        if (isset($countryMap[$normalized])) {
            return $this->finalize($countryMap[$normalized], $input, 'country_specific', $debug);
        }

        // ---------------------------------------------------------
        // 6) ADATBÁZIS EXACT MATCH
        // ---------------------------------------------------------
        $country = PdCountry::whereRaw('LOWER(name) = ?', [$normalized])->first();
        if ($country) {
            return $this->finalize($country->name, $input, 'exact_db', $debug, $country->id);
        }

        // ---------------------------------------------------------
        // 7) FUZZY MATCH
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchCountry($normalized);
        if ($fuzzy) {
            return $this->result(
                canonical: $fuzzy['canonical'],
                countryId: $fuzzy['country_id'],
                isoCode: $fuzzy['iso'],
                reason: 'fuzzy',
                input: $input,
                extra: ['score' => $fuzzy['score']],
                debug: $debug
            );
        }

        // ---------------------------------------------------------
        // 8) NINCS TALÁLAT → REVIEW
        // ---------------------------------------------------------
        return $this->result(
            canonical: null,
            countryId: null,
            isoCode: null,
            reason: 'no_match',
            input: $input,
            extra: ['needs_review' => true],
            debug: $debug
        );
    }

    // ---------------------------------------------------------
    // REGNO PREFIX → ORSZÁG
    // ---------------------------------------------------------

    private function detectFromRegNo(string $regNo): ?string
    {
        $regNo = strtoupper($regNo);

        $map = [
            'AKC'  => 'United States',
            'CKC'  => 'Canada',
            'KC'   => 'United Kingdom',
            'NZKC' => 'New Zealand',
            'ANKC' => 'Australia',
            'FCI'  => 'International',
            'MEOE' => 'Hungary',
            'CMKU' => 'Czech Republic',
            'SKJ'  => 'Slovakia',
            'NKK'  => 'Norway',
            'DKK'  => 'Denmark',
            'SV'   => 'Germany',
            'ÖKV'  => 'Austria',
            'ÖHZB' => 'Austria',
            'VDH'  => 'Germany',
            'LOF'  => 'France',
            'ROI'  => 'Italy',
            'RSR'  => 'Romania',
        ];

        foreach ($map as $prefix => $country) {
            if (str_starts_with($regNo, $prefix)) {
                return $country;
            }
        }

        return null;
    }

    // ---------------------------------------------------------
    // FINALIZE
    // ---------------------------------------------------------

    private function finalize(
        string $canonical,
        ?string $input,
        string $reason,
        bool $debug = false,
        ?int $countryId = null
    ): array {
        if (!$countryId) {
            $countryId = PdCountry::where('name', $canonical)->value('id');
        }

        $iso = $this->reverseIsoMap()[$canonical] ?? null;

        return $this->result(
            canonical: $canonical,
            countryId: $countryId,
            isoCode: $iso,
            reason: $reason,
            input: $input,
            extra: [],
            debug: $debug
        );
    }

    // ---------------------------------------------------------
    // RESULT
    // ---------------------------------------------------------

    private function result(
        ?string $canonical,
        ?int $countryId,
        ?string $isoCode,
        string $reason,
        ?string $input,
        array $extra = [],
        bool $debug = false
    ): array {
        $base = [
            'country'      => $canonical,
            'country_id'   => $countryId,
            'country_code' => $isoCode,
        ];

        if ($debug) {
            $base['debug'] = array_merge([
                'input'      => $input,
                'canonical'  => $canonical,
                'country_id' => $countryId,
                'iso_code'   => $isoCode,
                'reason'     => $reason,
            ], $extra);
        }

        return $base;
    }

    // ---------------------------------------------------------
    // NORMALIZÁLÁS
    // ---------------------------------------------------------

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $text = strtr($text, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','ő'=>'o','ú'=>'u','ü'=>'u','ű'=>'u',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ö'=>'o','Ő'=>'o','Ú'=>'u','Ü'=>'u','Ű'=>'u',
        ]);

        return preg_replace('/\s+/', ' ', $text);
    }

    // ---------------------------------------------------------
    // ALIASOK
    // ---------------------------------------------------------

    private function aliasMap(): array
    {
        return [
            'usa' => 'United States',
            'us'  => 'United States',
            'u.s.a.' => 'United States',
            'america' => 'United States',

            'uk' => 'United Kingdom',
            'england' => 'United Kingdom',
            'gb' => 'United Kingdom',
            'great britain' => 'United Kingdom',

            'deutschland' => 'Germany',
            'alemania' => 'Germany',

            'magyarország' => 'Hungary',
            'ungarn' => 'Hungary',

            'franciaország' => 'France',
            'frankreich' => 'France',
        ];
    }

    // ---------------------------------------------------------
    // ISO KÓDOK
    // ---------------------------------------------------------

    private function isoMap(): array
    {
        return [
            'hu' => 'Hungary',
            'de' => 'Germany',
            'fr' => 'France',
            'us' => 'United States',
            'gb' => 'United Kingdom',
            'uk' => 'United Kingdom',
            'ca' => 'Canada',
            'au' => 'Australia',
            'nz' => 'New Zealand',
            'it' => 'Italy',
            'es' => 'Spain',
            'pt' => 'Portugal',
            'pl' => 'Poland',
            'cz' => 'Czech Republic',
            'sk' => 'Slovakia',
            'ro' => 'Romania',
            'se' => 'Sweden',
            'no' => 'Norway',
            'dk' => 'Denmark',
            'fi' => 'Finland',
            'jp' => 'Japan',
        ];
    }

    private function reverseIsoMap(): array
    {
        $rev = [];
        foreach ($this->isoMap() as $code => $country) {
            $rev[$country] = strtoupper($code);
        }
        return $rev;
    }

    // ---------------------------------------------------------
    // ORSZÁG-SPECIFIKUS ALIASOK
    // ---------------------------------------------------------

    private function countrySpecificMap(): array
    {
        return [
            'holland' => 'Netherlands',
            'hollandia' => 'Netherlands',
            'nederland' => 'Netherlands',

            'schweiz' => 'Switzerland',
            'svajc' => 'Switzerland',
            'switzerland' => 'Switzerland',

            'austria' => 'Austria',
            'österreich' => 'Austria',
        ];
    }
}