<?php

namespace App\Services\Normalizers;

use App\Models\Kennels;
use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeKennelService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes kennel-normalizálás (globális, több kennelklub kompatibilis).
     */
    public function normalize(?string $rawKennel, ?string $country = null, bool $debug = false): array
    {
        if (!$rawKennel) {
            return $this->result(null, null, 'empty_input', null, [], $debug);
        }

        $input = trim($rawKennel);
        $clean = $this->normalizeText($input);

        // ---------------------------------------------------------
        // 1) Több kennelnév felismerése (&, /, ,)
        // ---------------------------------------------------------
        $kennels = $this->splitMultipleKennels($clean);
        $primary = trim($kennels[0]);

        // ---------------------------------------------------------
        // 2) Prefix / suffix / affix felismerés
        // ---------------------------------------------------------
        $parsed = $this->parseKennelName($primary);

        $canonical = $parsed['canonical'];
        $prefix    = $parsed['prefix'];
        $suffix    = $parsed['suffix'];
        $affix     = $parsed['affix'];
        $countryTag = $parsed['country'];
        $regno     = $parsed['regno'];
        $import    = $parsed['import'];

        // ---------------------------------------------------------
        // 3) Ország-specifikus aliasok
        // ---------------------------------------------------------
        $countryAlias = $this->countrySpecificAlias($country);
        if (isset($countryAlias[$canonical])) {
            return $this->finalize($countryAlias[$canonical], $input, 'country_alias', $debug);
        }

        // ---------------------------------------------------------
        // 4) Globális aliasok
        // ---------------------------------------------------------
        $alias = $this->globalAliasMap();
        if (isset($alias[$canonical])) {
            return $this->finalize($alias[$canonical], $input, 'global_alias', $debug);
        }

        // ---------------------------------------------------------
        // 5) EXACT DB MATCH
        // ---------------------------------------------------------
        $kennel = Kennels::whereRaw('LOWER(name) = ?', [mb_strtolower($canonical)])->first();
        if ($kennel) {
            return $this->result(
                canonical: $kennel->name,
                kennelId: $kennel->id,
                reason: 'exact_db_match',
                input: $input,
                extra: ['parsed' => $parsed],
                debug: $debug
            );
        }

        // ---------------------------------------------------------
        // 6) FUZZY MATCH
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchKennel($canonical);
        if ($fuzzy) {
            return $this->result(
                canonical: $fuzzy['canonical'],
                kennelId: $fuzzy['kennel_id'],
                reason: 'fuzzy_match',
                input: $input,
                extra: [
                    'score'  => $fuzzy['score'],
                    'parsed' => $parsed,
                ],
                debug: $debug
            );
        }

        // ---------------------------------------------------------
        // 7) NINCS TALÁLAT → REVIEW
        // ---------------------------------------------------------
        return $this->result(
            canonical: $canonical,
            kennelId: null,
            reason: 'no_match',
            input: $input,
            extra: [
                'parsed'       => $parsed,
                'needs_review' => true,
            ],
            debug: $debug
        );
    }

    // ---------------------------------------------------------
    // 3.0‑ÁS KENNEL PARSING ALRENDSZER
    // ---------------------------------------------------------

    private function splitMultipleKennels(string $input): array
    {
        return preg_split('/[,;&\/]+/', $input);
    }

    private function parseKennelName(string $clean): array
    {
        $original = $clean;

        // ---------------------------------------------------------
        // PREFIXEK (globális)
        // ---------------------------------------------------------
        $prefixes = [
            'von', 'vom', 'van', 'de', 'de la', 'des', 'du', 'of', 'from', 'del', 'della', 'da', 'di'
        ];

        $prefix = null;
        foreach ($prefixes as $p) {
            if (str_starts_with($clean, $p . ' ')) {
                $prefix = $p;
                $clean = trim(substr($clean, strlen($p)));
                break;
            }
        }

        // ---------------------------------------------------------
        // SUFFIXEK (globális)
        // ---------------------------------------------------------
        $suffixes = [
            'kennel', 'kennels', 'kennel club', 'kc', 'reg', 'reg.', 'reg’d', 'registered'
        ];

        $suffix = null;
        foreach ($suffixes as $s) {
            if (str_ends_with($clean, ' ' . $s)) {
                $suffix = $s;
                $clean = trim(substr($clean, 0, -strlen($s)));
                break;
            }
        }

        // ---------------------------------------------------------
        // ORSZÁGKÓD felismerés
        // ---------------------------------------------------------
        $country = null;
        if (preg_match('/\((HUN|USA|GER|ITA|FRA|ESP|GBR|CAN|AUS)\)$/i', $clean, $m)) {
            $country = strtoupper($m[1]);
            $clean = trim(preg_replace('/\(.+\)$/', '', $clean));
        }

        // ---------------------------------------------------------
        // REGNO felismerés (AKC #12345, CKC 987654)
        // ---------------------------------------------------------
        $regno = null;
        if (preg_match('/(AKC|CKC|UKC|ANKC|NZKC)\s*#?\s*([0-9]+)/i', $clean, $m)) {
            $regno = strtoupper($m[1]) . ' ' . $m[2];
            $clean = trim(str_ireplace($m[0], '', $clean));
        }

        // ---------------------------------------------------------
        // IMPORT jelölés
        // ---------------------------------------------------------
        $import = null;
        if (preg_match('/IMP\s+([A-Z]{3})/i', $clean, $m)) {
            $import = strtoupper($m[1]);
            $clean = trim(str_ireplace($m[0], '', $clean));
        }

        // ---------------------------------------------------------
        // AFFIX felismerés (pl. "vom Hause Steiner")
        // ---------------------------------------------------------
        $affix = null;
        if (preg_match('/(vom hause|von der|van het|de la|del la|della)\s+[A-Za-z0-9\s\-]+$/iu', $original, $m)) {
            $affix = trim($m[0]);
            $clean = trim(str_ireplace($m[0], '', $original));
        }

        return [
            'canonical' => trim($clean),
            'prefix'    => $prefix,
            'suffix'    => $suffix,
            'affix'     => $affix,
            'country'   => $country,
            'regno'     => $regno,
            'import'    => $import,
        ];
    }

    // ---------------------------------------------------------
    // ALIASOK
    // ---------------------------------------------------------

    private function globalAliasMap(): array
    {
        return [
            'kennel' => null,
            'unknown kennel' => null,
            'n/a' => null,
        ];
    }

    private function countrySpecificAlias(?string $country): array
    {
        $country = strtolower((string)$country);

        return match ($country) {
            'de', 'germany' => ['zwinger' => null],
            'fr', 'france'  => ['elevage' => null],
            'es', 'spain'   => ['criadero' => null],
            default => [],
        };
    }

    // ---------------------------------------------------------
    // EREDMÉNY
    // ---------------------------------------------------------

    private function result(
        ?string $canonical,
        ?int $kennelId,
        string $reason,
        ?string $input,
        array $extra = [],
        bool $debug = false
    ): array {
        $base = [
            'kennel_name' => $canonical,
            'kennel_id'   => $kennelId,
        ];

        if ($debug) {
            $base['debug'] = array_merge([
                'input'       => $input,
                'canonical'   => $canonical,
                'kennel_id'   => $kennelId,
                'reason'      => $reason,
            ], $extra);
        }

        return $base;
    }

    private function finalize(string $canonical, string $input, string $reason, bool $debug = false): array
    {
        $kennelId = Kennels::where('name', $canonical)->value('id');

        return $this->result(
            canonical: $canonical,
            kennelId: $kennelId,
            reason: $reason,
            input: $input,
            extra: [],
            debug: $debug
        );
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));

        $text = strtr($text, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','ő'=>'o','ú'=>'u','ü'=>'u','ű'=>'u',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ö'=>'o','Ő'=>'o','Ú'=>'u','Ü'=>'u','Ű'=>'u',
        ]);

        return preg_replace('/\s+/', ' ', $text);
    }
}