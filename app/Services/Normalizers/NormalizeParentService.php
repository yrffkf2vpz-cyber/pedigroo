<?php

namespace App\Services\Normalizers;

use App\Models\PdDog;
use App\Services\Normalizers\Support\FuzzyMatchService;

class NormalizeParentService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * A szülok normalizálása.
     */
    public function normalize(array $raw, bool $debug = false): array
    {
        return [
            'sire' => $this->normalizeOne($raw['sire'] ?? null, $debug),
            'dam'  => $this->normalizeOne($raw['dam']  ?? null, $debug),
        ];
    }

    /**
     * Egyetlen szülo normalizálása.
     */
    private function normalizeOne(?string $raw, bool $debug = false): array
    {
        if (!$raw) {
            return $this->result(null, null, null, null, 'empty_input', [], $debug);
        }

        $clean = trim($raw);

        // ---------------------------------------------------------
        // 1) Prefixek leválasztása (CH, JCH, INT CH, MULTI CH, stb.)
        // ---------------------------------------------------------
        $prefix = $this->extractPrefixes($clean);
        $clean  = $prefix['clean'];

        // ---------------------------------------------------------
        // 2) Országkód felismerése (HUN, USA, GER, stb.)
        // ---------------------------------------------------------
        $country = $this->extractCountry($clean);
        $clean   = $country['clean'];

        // ---------------------------------------------------------
        // 3) Import jelölések (IMP USA, IMPORT USA)
        // ---------------------------------------------------------
        $import = $this->extractImport($clean);
        $clean  = $import['clean'];

        // ---------------------------------------------------------
        // 4) Regisztrációs számok felismerése (több is lehet)
        // ---------------------------------------------------------
        $reg = $this->extractRegNos($clean);
        $clean = $reg['clean'];

        // ---------------------------------------------------------
        // 5) Kennelnév felismerése (prefix + suffix + FCI affixek)
        // ---------------------------------------------------------
        $kennel = $this->extractKennel($clean);
        $clean  = $kennel['clean'];

        // ---------------------------------------------------------
        // 6) Canonical név
        // ---------------------------------------------------------
        $canonicalName = trim($clean);

        // ---------------------------------------------------------
        // 7) ADATBÁZIS EXACT MATCH
        // ---------------------------------------------------------
        $dog = $this->matchDatabase($canonicalName, $reg['regnos']);

        if ($dog) {
            return $this->result(
                $dog->name,
                $dog->reg_no_clean,
                $dog->origin_country,
                $dog->id,
                'exact_db_match',
                [
                    'parsed' => compact('prefix','country','import','reg','kennel'),
                ],
                $debug
            );
        }

        // ---------------------------------------------------------
        // 8) FUZZY MATCH
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchParent(
            $canonicalName,
            $reg['regnos'][0] ?? null,
            $country['country']
        );

        if ($fuzzy) {
            return $this->result(
                $fuzzy['canonical'],
                $fuzzy['reg_no'],
                $fuzzy['country'],
                $fuzzy['dog_id'],
                'fuzzy_match',
                [
                    'score'  => $fuzzy['score'],
                    'parsed' => compact('prefix','country','import','reg','kennel'),
                ],
                $debug
            );
        }

        // ---------------------------------------------------------
        // 9) NINCS TALÁLAT ? REVIEW
        // ---------------------------------------------------------
        return $this->result(
            $canonicalName,
            $reg['regnos'][0] ?? null,
            $country['country'],
            null,
            'no_match',
            [
                'needs_review' => true,
                'parsed'       => compact('prefix','country','import','reg','kennel'),
            ],
            $debug
        );
    }

    // ---------------------------------------------------------
    // PARSING ALRENDSZER (3.0)
    // ---------------------------------------------------------

    private function extractPrefixes(string $name): array
    {
        $patterns = [
            'INT CH', 'MULTI CH', 'GRCH', 'HCH', 'JCH', 'CH'
        ];

        foreach ($patterns as $p) {
            if (stripos($name, $p) === 0) {
                return [
                    'prefix' => $p,
                    'clean'  => trim(substr($name, strlen($p))),
                ];
            }
        }

        return ['prefix' => null, 'clean' => $name];
    }

    private function extractCountry(string $name): array
    {
        if (preg_match('/\((HUN|USA|GER|ITA|FRA|ESP|GBR|CAN|AUS)\)$/i', $name, $m)) {
            return [
                'country' => strtoupper($m[1]),
                'clean'   => trim(preg_replace('/\(.+\)$/', '', $name)),
            ];
        }

        return ['country' => null, 'clean' => $name];
    }

    private function extractImport(string $name): array
    {
        if (preg_match('/IMP\s+([A-Z]{3})/i', $name, $m)) {
            return [
                'import' => strtoupper($m[1]),
                'clean'  => trim(str_ireplace($m[0], '', $name)),
            ];
        }

        return ['import' => null, 'clean' => $name];
    }

    private function extractRegNos(string $name): array
    {
        $regnos = [];

        if (preg_match_all('/(AKC|CKC|MEOE|MET|LOF|FCI|JKC|ANKC|NZKC|KC|VDH|ÖKV|CMKU|SKJ|NKK|DKK)[\s\-]*([A-Z0-9\/\-]+)/i', $name, $m)) {
            foreach ($m[2] as $r) {
                $regnos[] = strtoupper($r);
            }
            $name = trim(str_ireplace($m[0], '', $name));
        }

        return [
            'regnos' => $regnos,
            'clean'  => $name,
        ];
    }

    private function extractKennel(string $name): array
    {
        // suffix kennelnév
        if (preg_match('/(vom|von|of|du|de la|des)\s+[A-Za-z0-9\s\-]+$/iu', $name, $m)) {
            return [
                'kennel' => trim($m[0]),
                'clean'  => trim(str_ireplace($m[0], '', $name)),
            ];
        }

        // prefix kennelnév
        $parts = explode(' ', $name);
        if (count($parts) > 2) {
            $prefix = strtolower($parts[0] . ' ' . $parts[1]);
            $known  = ['silver dream', 'blue river', 'von haus', 'vom haus'];

            if (in_array($prefix, $known)) {
                return [
                    'kennel' => $parts[0] . ' ' . $parts[1],
                    'clean'  => implode(' ', array_slice($parts, 2)),
                ];
            }
        }

        return ['kennel' => null, 'clean' => $name];
    }

    private function matchDatabase(string $name, array $regnos): ?PdDog
    {
        return PdDog::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->orWhereIn('reg_no_clean', $regnos)
            ->first();
    }

    private function result(
        ?string $name,
        ?string $regNo,
        ?string $country,
        ?int $parentId,
        string $reason,
        array $extra = [],
        bool $debug = false
    ): array {
        $base = [
            'name'      => $name,
            'reg_no'    => $regNo,
            'country'   => $country,
            'parent_id' => $parentId,
        ];

        if ($debug) {
            $base['debug'] = array_merge([
                'canonical_name' => $name,
                'reg_no'         => $regNo,
                'country'        => $country,
                'parent_id'      => $parentId,
                'reason'         => $reason,
            ], $extra);
        }

        return $base;
    }
}