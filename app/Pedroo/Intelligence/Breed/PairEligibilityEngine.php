<?php

namespace App\Pedroo\Intelligence\Breed;

use App\Models\Dog;
use App\Pedroo\Intelligence\Color\ColorEngine;
use App\Pedroo\Intelligence\Health\HealthEngine;
use App\Pedroo\Intelligence\COI\COIEngine;

class PairEligibilityEngine
{
    public function __construct(
        protected BreedEngine $breedEngine,
        protected ColorEngine $colorEngine,
        protected HealthEngine $healthEngine,
        protected COIEngine $coiEngine,
    ) {}

    /**
     * Teljes párosítás-ellenőrzés
     */
    public function checkPair(Dog $sire, Dog $dam): array
    {
        $breedId = $sire->breed_id;
        $rules   = $this->breedEngine->loadRules($breedId);

        // 1) Egyedi tenyészthetőség
        $sireRules = $this->breedEngine->checkDogEligibility($sire);
        $damRules  = $this->breedEngine->checkDogEligibility($dam);

        // 2) Genetikai kockázatok (szín + genetika)
        $geneticRisks = $this->checkGeneticRisks($sire, $dam, $rules);

        // 3) Egészségügyi kockázatok
        $healthRisks = $this->healthEngine->checkPairHealth($sire, $dam);

        // 4) COI számítása
        $coi = $this->coiEngine->calculatePairCoi($sire, $dam);

        // 5) COI limit
        $coiLimit  = $rules['inbreeding_limit'] ?? null;
        $coiStatus = $coiLimit !== null && $coi > $coiLimit ? 'exceeds_limit' : 'ok';

        // 6) Végső döntés
        $pairAllowed = $this->isPairAllowed(
            $sireRules,
            $damRules,
            $geneticRisks,
            $healthRisks,
            $coi,
            $coiLimit
        );

        // 7) Egységes eredménystruktúra
        return [
            'pair_allowed' => $pairAllowed,

            'coi' => [
                'value'  => $coi,
                'limit'  => $coiLimit,
                'status' => $coiStatus,
            ],

            'sire' => [
                'id'          => $sire->id,
                'eligibility' => $sireRules,
            ],

            'dam' => [
                'id'          => $dam->id,
                'eligibility' => $damRules,
            ],

            'genetic_risks' => $geneticRisks,
            'health_risks'  => $healthRisks,
        ];
    }

    /**
     * Genetikai + szín + minta kockázatok
     */
    private function checkGeneticRisks(Dog $sire, Dog $dam, array $rules): array
    {
        $risks = [];

        // --- Merle × Merle (hard tiltás) ---
        if (
            ($sire->genotypes['M'] ?? null) === 'M/m' &&
            ($dam->genotypes['M'] ?? null) === 'M/m'
        ) {
            $risks[] = 'double_merle_risk';
        }

        // --- e/e × e/e (jelzés) ---
        if (
            ($sire->genotypes['E'] ?? null) === 'e/e' &&
            ($dam->genotypes['E'] ?? null) === 'e/e'
        ) {
            $risks[] = 'all_red_litter';
        }

        // --- d/d × d/d (jelzés) ---
        if (
            ($sire->genotypes['D'] ?? null) === 'd/d' &&
            ($dam->genotypes['D'] ?? null) === 'd/d'
        ) {
            $risks[] = 'all_dilute_litter';
        }

        // --- Tiltott színkombinációk ---
        if (!empty($rules['forbidden_color_combinations'])) {
            $combos = json_decode($rules['forbidden_color_combinations'], true);

            foreach ($combos as $combo) {
                if (
                    $sire->color === $combo['sire'] &&
                    $dam->color  === $combo['dam']
                ) {
                    $risks[] = 'forbidden_color_combination';
                }
            }
        }

        // --- Tiltott genetikai kombinációk ---
        if (!empty($rules['forbidden_genotype_combinations'])) {
            $combos = json_decode($rules['forbidden_genotype_combinations'], true);

            foreach ($combos as $combo) {
                $gene = $combo['gene'];

                if (
                    ($sire->genotypes[$gene] ?? null) === $combo['sire'] &&
                    ($dam->genotypes[$gene] ?? null) === $combo['dam']
                ) {
                    $risks[] = 'forbidden_genotype_combination';
                }
            }
        }

        return $risks;
    }

    /**
     * Végső döntés
     */
    private function isPairAllowed(
        array $sireRules,
        array $damRules,
        array $geneticRisks,
        array $healthRisks,
        float $coi,
        ?float $coiLimit
    ): bool {
        // Egyedi tiltások
        if (in_array(false, $sireRules, true) || in_array(false, $damRules, true)) {
            return false;
        }

        // Double merle tiltás
        if (in_array('double_merle_risk', $geneticRisks, true)) {
            return false;
        }

        // Tiltott színkombináció
        if (in_array('forbidden_color_combination', $geneticRisks, true)) {
            return false;
        }

        // Tiltott genetikai kombináció
        if (in_array('forbidden_genotype_combination', $geneticRisks, true)) {
            return false;
        }

        // Egészségügyi tiltás
        if ($healthRisks['forbidden'] ?? false) {
            return false;
        }

        // COI limit tiltás (hard limit)
        if ($coiLimit !== null && $coi > $coiLimit) {
            return false;
        }

        return true;
    }
}