<?php

namespace App\Services\Normalizers;

use App\Models\PdOwner;
use App\Models\Kennels;
use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeBreederService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Teljes tenyésztő-normalizálás:
     * - többnyelvű aliasok
     * - fuzzy match
     * - canonical form
     * - breeder_id lookup
     * - kennel kapcsolás
     * - név + kennelnév szétválasztás
     */
    public function normalize(?string $rawBreeder, ?string $country = null): array
    {
        $input = $rawBreeder ? trim($rawBreeder) : null;

        if (!$input) {
            return $this->result(null, null, null, 'empty input', $input);
        }

        // 1) LOWERCASE + TRIM + NORMALIZE DIACRITICS
        $normalized = $this->normalizeText($input);

        // 2) ALIAS MAP (többnyelvű)
        $alias = $this->aliasMap();
        if (isset($alias[$normalized])) {
            $canonical = $alias[$normalized];
            return $this->finalize($canonical, $input, 'alias match');
        }

        // 3) ORSZÁG-SPECIFIKUS ÁTNEVEZÉSEK
        $countryMap = $this->countrySpecificMap($country);
        if (isset($countryMap[$normalized])) {
            $canonical = $countryMap[$normalized];
            return $this->finalize($canonical, $input, 'country-specific alias');
        }

        // 4) KENNEL + BREEDER SZÉTVÁLASZTÁS
        $split = $this->splitKennelAndBreeder($input);
        $canonicalName   = $split['breeder'];
        $canonicalKennel = $split['kennel'];

        // 5) BREEDER ADATBÁZIS MATCH
        $breeder = PdOwner::whereRaw('LOWER(name) = ?', [mb_strtolower($canonicalName)])->first();
        if ($breeder) {
            return $this->result(
                canonical: $breeder->name,
                breederId: $breeder->id,
                kennelId: $this->resolveKennel($canonicalKennel),
                reason: 'exact db match',
                input: $input
            );
        }

        // 6) FUZZY MATCH
        $fuzzyMatch = $this->fuzzy->matchBreeder($canonicalName);
        if ($fuzzyMatch) {
            return $this->result(
                canonical: $fuzzyMatch['canonical'],
                breederId: $fuzzyMatch['breeder_id'],
                kennelId: $this->resolveKennel($canonicalKennel),
                reason: 'fuzzy match',
                input: $input,
                extra: ['score' => $fuzzyMatch['score']]
            );
        }

        // 7) NINCS TALÁLAT → REVIEW
        return $this->result(
            canonical: $canonicalName,
            breederId: null,
            kennelId: $this->resolveKennel($canonicalKennel),
            reason: 'no match',
            input: $input,
            extra: ['needs_review' => true]
        );
    }

    private function resolveKennel(?string $kennelName): ?int
    {
        if (!$kennelName) {
            return null;
        }

        $kennel = Kennels::whereRaw('LOWER(name) = ?', [mb_strtolower($kennelName)])->first();

        if ($kennel) {
            return $kennel->id;
        }

        // fuzzy kennel match
        $fuzzy = $this->fuzzy->matchKennel($kennelName);
        return $fuzzy['kennel_id'] ?? null;
    }

    private function splitKennelAndBreeder(string $input): array
    {
        // Formátumok:
        // "Kennelname – Breeder Name"
        // "Breeder Name, Kennelname"
        // "Breeder Name (Kennelname)"
        // "Breeder Name of Kennelname"
        // "Breeder Name vom Kennelname"

        $patterns = [
            '/(.+)\s*[-–]\s*(.+)/u',          // Kennel – Breeder
            '/(.+),\s*(.+)/u',                // Breeder, Kennel
            '/(.+)\((.+)\)/u',                // Breeder (Kennel)
            '/(.+)\sof\s(.+)/iu',             // Breeder of Kennel
            '/(.+)\svom\s(.+)/iu',            // Breeder vom Kennel
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $input, $m)) {
                $a = trim($m[1]);
                $b = trim($m[2]);

                // Heurisztika: melyik a kennel?
                if (str_contains(mb_strtolower($b), 'kennel')) {
                    return ['breeder' => $a, 'kennel' => $b];
                }
                if (str_contains(mb_strtolower($a), 'kennel')) {
                    return ['breeder' => $b, 'kennel' => $a];
                }

                // fallback: breeder = első, kennel = második
                return ['breeder' => $a, 'kennel' => $b];
            }
        }

        return ['breeder' => $input, 'kennel' => null];
    }

    private function result(
        ?string $canonical,
        ?int $breederId,
        ?int $kennelId,
        string $reason,
        ?string $input,
        array $extra = []
    ): array {
        return [
            'breeder_name' => $canonical,
            'breeder_id'   => $breederId,
            'kennel_id'    => $kennelId,

            'debug' => array_merge([
                'input'       => $input,
                'canonical'   => $canonical,
                'breeder_id'  => $breederId,
                'kennel_id'   => $kennelId,
                'reason'      => $reason,
            ], $extra),
        ];
    }

    private function normalizeText(string $text): string
    {
        $text = mb_strtolower(trim($text));

        // ékezetek eltávolítása
        $text = strtr($text, [
            'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ö'=>'o','ő'=>'o','ú'=>'u','ü'=>'u','ű'=>'u',
            'Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ö'=>'o','Ő'=>'o','Ú'=>'u','Ü'=>'u','Ű'=>'u',
        ]);

        return preg_replace('/\s+/', ' ', $text);
    }

    private function aliasMap(): array
    {
        return [
            // gyakori rövidítések
            'breeder unknown' => null,
            'unknown breeder' => null,
            'n/a' => null,
            'none' => null,

            // többnyelvű aliasok
            'züchter unbekannt' => null,
            'éleveur inconnu' => null,
            'criador desconocido' => null,
        ];
    }

    private function countrySpecificMap(?string $country): array
    {
        $country = strtolower((string)$country);

        return match ($country) {
            'de', 'germany' => [
                'züchter' => null,
            ],
            'fr', 'france' => [
                'éleveur' => null,
            ],
            'es', 'spain' => [
                'criador' => null,
            ],
            default => [],
        };
    }
}