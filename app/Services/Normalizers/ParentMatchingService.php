<?php

namespace App\Services\Normalizers;

use App\Models\PdDog;
use App\Services\Normalizers\Support\FuzzyMatchService;

class ParentMatchingService
{
    public function __construct(
        protected FuzzyMatchService $fuzzy
    ) {}

    /**
     * Canonical parent input:
     * [
     *   'name'    => 'Silver Dream Aussie Blue Sky',
     *   'reg_no'  => 'MET12345/20',
     *   'country' => 'Hungary',
     *   'kennel'  => 'Silver Dream'
     * ]
     */
    public function match(array $parent): array
    {
        $input = $parent;

        // Ha semmi nincs → nincs mit keresni
        if (!$input['name'] && !$input['reg_no']) {
            return $this->result(null, 'no_input', $input);
        }

        // ---------------------------------------------------------
        // 1) REGNO EXACT MATCH (LEGJOBB)
        // ---------------------------------------------------------
        if ($input['reg_no']) {
            $dog = PdDog::where('reg_no_clean', $input['reg_no'])->first();

            if ($dog) {
                return $this->result($dog, 'regno_exact', $input, 1.00);
            }
        }

        // ---------------------------------------------------------
        // 2) NÉV + KENNEL + ORSZÁG KOMBINÁLT EXACT
        // ---------------------------------------------------------
        $dog = PdDog::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($input['name'])])
            ->when($input['kennel'], fn($q) =>
                $q->orWhereRaw('LOWER(kennel_name) = ?', [mb_strtolower($input['kennel'])])
            )
            ->when($input['country'], fn($q) =>
                $q->orWhere('origin_country', $input['country'])
            )
            ->first();

        if ($dog) {
            return $this->result($dog, 'combined_exact', $input, 0.90);
        }

        // ---------------------------------------------------------
        // 3) FUZZY MATCH (név + regno + country)
        // ---------------------------------------------------------
        $fuzzy = $this->fuzzy->matchParent(
            name:    $input['name'],
            regNo:   $input['reg_no'],
            country: $input['country']
        );

        if ($fuzzy) {
            $dog = PdDog::find($fuzzy['dog_id']);

            if ($dog) {
                return $this->result(
                    $dog,
                    'fuzzy_match',
                    $input,
                    $fuzzy['score'],
                    ['fuzzy' => $fuzzy]
                );
            }
        }

        // ---------------------------------------------------------
        // 4) WEIGHTED CANDIDATE SCORING (3.0 ÚJ)
        // ---------------------------------------------------------
        $candidates = PdDog::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%' . mb_strtolower($input['name']) . '%'])
            ->limit(10)
            ->get();

        if ($candidates->count() > 1) {
            $scored = [];

            foreach ($candidates as $c) {
                $score = 0;

                // név hasonlóság
                similar_text(
                    mb_strtolower($input['name']),
                    mb_strtolower($c->name),
                    $nameScore
                );
                $score += $nameScore / 100 * 0.5;

                // kennel egyezés
                if ($input['kennel'] && mb_strtolower($input['kennel']) === mb_strtolower($c->kennel_name)) {
                    $score += 0.3;
                }

                // ország egyezés
                if ($input['country'] && $input['country'] === $c->origin_country) {
                    $score += 0.2;
                }

                $scored[] = [
                    'dog'   => $c,
                    'score' => $score,
                ];
            }

            usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

            // ha a legjobb jelölt nagyon erős
            if ($scored[0]['score'] >= 0.75) {
                return $this->result(
                    $scored[0]['dog'],
                    'weighted_match',
                    $input,
                    $scored[0]['score'],
                    ['candidates' => $scored]
                );
            }

            // különben ambiguous
            return [
                'matched_id' => null,
                'matched'    => null,
                'debug'      => [
                    'reason'     => 'ambiguous',
                    'input'      => $input,
                    'candidates' => collect($scored)->pluck('dog.id'),
                ],
            ];
        }

        // ---------------------------------------------------------
        // 5) NINCS TALÁLAT
        // ---------------------------------------------------------
        return $this->result(null, 'no_match', $input);
    }

    private function result(?PdDog $dog, string $reason, array $input, float $score = null, array $extra = []): array
    {
        return [
            'matched_id' => $dog?->id,
            'matched'    => $dog ? [
                'name'    => $dog->name,
                'reg_no'  => $dog->reg_no_clean,
                'country' => $dog->origin_country,
                'kennel'  => $dog->kennel_name,
            ] : null,

            'debug' => array_merge([
                'reason' => $reason,
                'score'  => $score,
                'input'  => $input,
            ], $extra),
        ];
    }
}