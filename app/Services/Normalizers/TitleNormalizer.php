<?php

namespace App\Services\Normalizers;

class TitleNormalizer
{
    /**
     * Canonical címek listája.
     */
    private array $map = [
        // Champion titles
        'CH'        => ['CH', 'HCH', 'JCH', 'HJCH', 'INT CH', 'MCH', 'MULTI CH', 'GRCH', 'HVCH', 'WCH'],
        
        // CAC system
        'CAC'       => ['CAC'],
        'RESCAC'    => ['RESCAC', 'RES CAC', 'RCAC'],
        'CACIB'     => ['CACIB'],
        'RESCACIB'  => ['RESCACIB', 'RES CACIB', 'RCACIB'],

        // Junior / Puppy / Veteran
        'HPJ'       => ['HPJ', 'JUNIOR WINNER', 'BEST JUNIOR'],
        'HFGY'      => ['HFGY', 'HFGY1', 'HFGY2', 'HFGY3', 'HFGY4'],

        // Show results
        'BOB'       => ['BOB', 'BEST OF BREED'],
        'BOS'       => ['BOS', 'BEST OPPOSITE SEX'],
        'BOG'       => ['BOG', 'BEST OF GROUP'],
        'BIG'       => ['BIG', 'BEST IN GROUP'],
        'BIS'       => ['BIS', 'BEST IN SHOW'],
        'RESBIS'    => ['RES BIS', 'RESBIS', 'RESERVE BIS'],
        'PUPPY BIS' => ['PUPPY BIS', 'BEST PUPPY IN SHOW'],
        'BABY BIS'  => ['BABY BIS', 'BEST BABY IN SHOW'],
        'VETERAN BIS' => ['VETERAN BIS', 'BEST VETERAN IN SHOW'],
    ];

    /**
     * Teljes címnormalizálás.
     */
    public function normalize(array $promotions): array
    {
        $input = $promotions;
        $titles = [];

        foreach ($promotions as $raw) {
            $clean = $this->cleanString($raw);

            // több cím felismerése egy stringben
            $parts = preg_split('/[,\.\;\|\/]+|\s{2,}/', $clean);

            foreach ($parts as $p) {
                $p = trim($p);
                if ($p === '') continue;

                $canonical = $this->matchTitle($p);
                if ($canonical) {
                    $titles[] = $canonical;
                }
            }
        }

        // egyedi címek
        $titles = array_values(array_unique($titles));

        return [
            'titles' => $titles,
            'debug'  => [
                'input'      => $input,
                'normalized' => $titles,
            ],
        ];
    }

    /**
     * Cím fuzzy match.
     */
    private function matchTitle(string $raw): ?string
    {
        $upper = strtoupper($raw);

        foreach ($this->map as $canonical => $variants) {
            foreach ($variants as $v) {
                if ($upper === strtoupper($v)) {
                    return $canonical;
                }
                if (str_contains($upper, strtoupper($v))) {
                    return $canonical;
                }
            }
        }

        return null;
    }

    /**
     * Zaj eltávolítása.
     */
    private function cleanString(string $s): string
    {
        return trim(
            preg_replace([
                '/\(.+?\)/',   // remove parentheses
                '/\s+/',       // normalize spaces
            ], ' ', $s)
        );
    }
}