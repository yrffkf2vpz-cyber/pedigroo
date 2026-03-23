<?php

namespace App\Pedroo\Intelligence\Color;

use Illuminate\Support\Facades\DB;

class ColorEngine
{
    public function __construct(
        protected GenotypeResolver $resolver,
        protected PuppyPredictor $predictor,
        protected PhenotypeMapper $phenotypeMapper,
    ) {}

    /**
     * Litter planner: expected puppy colors & genetic risks
     */
    public function predictLitter(int $breedId, string $sireColor, string $damColor): array
    {
        // 1) Parents' genotypes
        $sireGenotypes = $this->resolver->resolve($sireColor, $breedId);
        $damGenotypes  = $this->resolver->resolve($damColor, $breedId);

        // 2) Per-gene Punnett results
        $perGene = $this->predictor->predict($sireGenotypes, $damGenotypes);

        // 3) Combined genetic profiles
        $profiles = $this->combineGeneProfiles($perGene);

        // 4) Enrich with descriptions, risks, and color names
        $enriched = $this->enrichProfilesWithDescriptionsAndColor($breedId, $profiles);

        return [
            'sire'    => [
                'color'     => $sireColor,
                'genotypes' => $sireGenotypes,
            ],
            'dam'     => [
                'color'     => $damColor,
                'genotypes' => $damGenotypes,
            ],
            'puppies' => $enriched,
        ];
    }

    private function combineGeneProfiles(array $perGene): array
    {
        $profiles = [
            [
                'genotypes'  => [],
                'probability'=> 100.0,
            ],
        ];

        foreach ($perGene as $gene => $genotypeOptions) {
            $newProfiles = [];

            foreach ($profiles as $profile) {
                foreach ($genotypeOptions as $genotype => $percent) {
                    $newProfiles[] = [
                        'genotypes'  => $profile['genotypes'] + [$gene => $genotype],
                        'probability'=> $profile['probability'] * ($percent / 100),
                    ];
                }
            }

            $profiles = $newProfiles;
        }

        return $profiles;
    }

    private function enrichProfilesWithDescriptionsAndColor(int $breedId, array $profiles): array
    {
        $rules = DB::table('pd_breed_color_genetics')
            ->where('breed_id', $breedId)
            ->get()
            ->groupBy('gene');

        return collect($profiles)
            ->map(function ($profile) use ($rules) {
                $descriptions = [];
                $riskFlags    = [];

                foreach ($profile['genotypes'] as $gene => $genotype) {
                    $geneRules = $rules->get($gene);

                    if (!$geneRules) {
                        continue;
                    }

                    $match = $geneRules->firstWhere('genotype', $genotype);

                    if ($match && $match->description) {
                        $descriptions[] = $match->description;

                        if (
                            str_contains(strtolower($match->description), 'risk') ||
                            str_contains(strtolower($match->description), 'not recommended')
                        ) {
                            $riskFlags[] = $match->description;
                        }
                    }
                }

                // phenotype mapping → color key → translated color name
                $colorKey  = $this->phenotypeMapper->map($profile['genotypes']);
                $colorName = __('genetics.' . $colorKey);

                return [
                    'genotypes'    => $profile['genotypes'],
                    'probability'  => $profile['probability'],
                    'color_key'    => $colorKey,
                    'color'        => $colorName,
                    'descriptions' => $descriptions,
                    'risks'        => array_values(array_unique($riskFlags)),
                ];
            })
            ->sortByDesc('probability')
            ->values()
            ->all();
    }
}