<?php

namespace App\Services\Normalizers;

use App\Models\PdOwner;
use App\Models\Kennels;
use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeOwnerService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes owner-normalizálás (globális, több kennel klub kompatibilis).
     */
    public function normalize(?string $rawOwner, ?string $country = null, bool $debug = false): array
    {
        $input = $rawOwner ? trim($rawOwner) : null;

        if (!$input) {
            return $this->result(null, null, null, 'empty_input', $input, [], $debug);
        }

        // ---------------------------------------------------------
        // 1) Több tulajdonos felismerése ("," ";" "/" "and" "&")
        // ---------------------------------------------------------
        $owners = $this->splitMultipleOwners($input);

        // Csak az elsődleges tulajdonost normalizáljuk (Pedigroo 3.0 logika)
        $primary = trim($owners[0]);

        // ---------------------------------------------------------
        // 2) Normalizált lowercase + diakritika eltávolítás
        // ---------------------------------------------------------
        $normalized = $this->normalizeText($primary);

        // ---------------------------------------------------------
        // 3) Többnyelvű aliasok
        // ---------------------------------------------------------
        $alias = $this->aliasMap();
        if (isset($alias[$normalized])) {
            return $this->finalize($alias[$normalized], $input, 'alias_match', null, null, $debug);
        }

        // ---------------------------------------------------------
        // 4) Ország-specifikus aliasok
        // ---------------------------------------------------------
        $countryMap = $this->countrySpecificMap($country);
        if (isset($countryMap[$normalized])) {
            return $this->finalize($countryMap[$normalized], $input, 'country_specific_alias', null, null, $debug);
        }

        // ---------------------------------------------------------
        // 5) Kennel + Owner szétválasztása (globális minták)
        // ---------------------------------------------------------
        $split = $this->splitKennelAndOwner($primary);
        $canonicalOwner  = $split['owner'];
        $canonicalKennel = $split['kennel'];

        // ---------------------------------------------------------
        // 6) OWNER EXACT MATCH
        // ---------------------------------------------------------
        $owner = PdOwner::whereRaw('LOWER(name) = ?', [mb_strtolower($canonicalOwner)])->first();

        if ($owner) {
            return $this->result(
                canonical: $owner->name,
                ownerId: $owner->id,
                kennelId: $this->resolveKennel($canonicalKennel),
                reason: 'exact_db_match',
                input: $input,
                extra: ['parsed' => $split],
                debug: $debug
            );
        }

        // ---------------------------------------------------------
        // 7) FUZZY MATCH
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchOwner($canonicalOwner);

        if ($fuzzy) {
            return $this->result(
                canonical: $fuzzy['canonical'],
                ownerId: $fuzzy['owner_id'],
                kennelId: $this->resolveKennel($canonicalKennel),
                reason: 'fuzzy_match',
                input: $input,
                extra: [
                    'score'  => $fuzzy['score'],
                    'parsed' => $split,
                ],
                debug: $debug
            );
        }

        // ---------------------------------------------------------
        // 8) NINCS TALÁLAT → REVIEW
        // ---------------------------------------------------------
        return $this->result(
            canonical: $canonicalOwner,
            ownerId: null,
            kennelId: $this->resolveKennel($canonicalKennel),
            reason: 'no_match',
            input: $input,
            extra: [
                'parsed'       => $split,
                'needs_review' => true,
            ],
            debug: $debug
        );
    }

    // ---------------------------------------------------------
    // SEGÉDMETÓDUSOK
    // ---------------------------------------------------------

    private function splitMultipleOwners(string $input): array
    {
        return preg_split('/[,;\/&]| and /i', $input);
    }

    private function resolveKennel(?string $kennelName): ?int
    {
        if (!$kennelName) return null;

        $kennel = Kennels::whereRaw('LOWER(name) = ?', [mb_strtolower($kennelName)])->first();
        if ($kennel) return $kennel->id;

        $fuzzy = $this->fuzzy->matchKennel($kennelName);
        return $fuzzy['kennel_id'] ?? null;
    }

    private function splitKennelAndOwner(string $input): array
    {
        // Globális kennel minták
        $patterns = [
            '/(.+)\s*[-–]\s*(.+)/u',
            '/(.+),\s*(.+)/u',
            '/(.+)\((.+)\)/u',
            '/(.+)\sof\s(.+)/iu',
            '/(.+)\svom\s(.+)/iu',
            '/(.+)\svan\s(.+)/iu',
            '/(.+)\sfrom\s(.+)/iu',
            '/(.+)\skennel\s(.+)/iu',
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $input, $m)) {
                $a = trim($m[1]);
                $b = trim($m[2]);

                if (str_contains(mb_strtolower($b), 'kennel')) {
                    return ['owner' => $a, 'kennel' => $b];
                }
                if (str_contains(mb_strtolower($a), 'kennel')) {
                    return ['owner' => $b, 'kennel' => $a];
                }

                return ['owner' => $a, 'kennel' => $b];
            }
        }

        return ['owner' => $input, 'kennel' => null];
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

    private function aliasMap(): array
    {
        return [
            'owner unknown' => null,
            'unknown owner' => null,
            'n/a' => null,
            'none' => null,
            'züchter' => null,
            'éleveur' => null,
            'criador' => null,
            'breeder unknown' => null,
            'no owner' => null,
        ];
    }

    private function countrySpecificMap(?string $country): array
    {
        $country = strtolower((string)$country);

        return match ($country) {
            'de', 'germany' => ['züchter' => null],
            'fr', 'france'  => ['éleveur' => null],
            'es', 'spain'   => ['criador' => null],
            default => [],
        };
    }

    private function result(
        ?string $canonical,
        ?int $ownerId,
        ?int $kennelId,
        string $reason,
        ?string $input,
        array $extra = [],
        bool $debug = false
    ): array {
        $base = [
            'owner_name' => $canonical,
            'owner_id'   => $ownerId,
            'kennel_id'  => $kennelId,
        ];

        if ($debug) {
            $base['debug'] = array_merge([
                'input'       => $input,
                'canonical'   => $canonical,
                'owner_id'    => $ownerId,
                'kennel_id'   => $kennelId,
                'reason'      => $reason,
            ], $extra);
        }

        return $base;
    }

    private function finalize(
        ?string $canonical,
        ?string $input,
        string $reason,
        ?int $ownerId = null,
        ?int $kennelId = null,
        bool $debug = false
    ): array {
        return $this->result($canonical, $ownerId, $kennelId, $reason, $input, [], $debug);
    }
}