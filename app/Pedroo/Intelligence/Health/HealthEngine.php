<?php

namespace App\Pedroo\Intelligence\Health;

use App\Models\Dog;
use App\Models\DogHealthResult;
use App\Models\PdBreedRulesHealth;

class HealthEngine
{
    public function loadBreedHealthRules(int $breedId)
    {
        return PdBreedRulesHealth::where('breed_id', $breedId)->get();
    }

    /**
     * Egy kutya egészségügyi megfelelősége
     */
    public function checkDogHealth(Dog $dog): array
    {
        $rules = $this->loadBreedHealthRules($dog->breed_id);
        $results = DogHealthResult::where('dog_id', $dog->id)
            ->pluck('result', 'test_type')
            ->toArray();

        $status = [];

        foreach ($rules as $rule) {
            $test = $rule->test_type;
            $mandatory = (bool)$rule->mandatory;
            $dogResult = $results[$test] ?? null;

            $ok = true;

            // Kötelező, de nincs eredmény
            if ($mandatory && $dogResult === null) {
                $ok = false;
            }

            // Klinikai teszt (min/max értékkel)
            if ($rule->min_result !== null || $rule->max_result !== null) {
                if ($dogResult !== null) {
                    if ($rule->min_result !== null && strcmp($dogResult, $rule->min_result) < 0) {
                        $ok = false;
                    }
                    if ($rule->max_result !== null && strcmp($dogResult, $rule->max_result) > 0) {
                        $ok = false;
                    }
                }
            }

            // Genetikai teszt (min/max NULL)
            if ($rule->min_result === null && $rule->max_result === null) {
                if ($dogResult !== null) {
                    $lower = strtolower($dogResult);
                    if (!in_array($lower, ['clear', 'carrier', 'affected'], true)) {
                        $ok = false;
                    }
                }
            }

            $status[$test] = [
                'mandatory' => $mandatory,
                'result'    => $dogResult,
                'ok'        => $ok,
            ];
        }

        return $status;
    }

    /**
     * Párosítás egészségügyi kockázatai
     */
    public function checkPairHealth(Dog $sire, Dog $dam): array
    {
        $sireResults = DogHealthResult::where('dog_id', $sire->id)
            ->pluck('result', 'test_type')
            ->toArray();

        $damResults = DogHealthResult::where('dog_id', $dam->id)
            ->pluck('result', 'test_type')
            ->toArray();

        $warnings = [];
        $forbidden = false;

        foreach ($sireResults as $test => $sireResult) {
            $damResult = $damResults[$test] ?? null;
            if ($damResult === null) {
                continue;
            }

            $sr = strtolower($sireResult);
            $dr = strtolower($damResult);

            // Genetikai tesztek: carrier × carrier → tiltott
            if (in_array($sr, ['carrier', 'affected'], true) &&
                in_array($dr, ['carrier', 'affected'], true)) {

                $warnings[] = $test . '_high_risk';
                $forbidden = true;
            }
        }

        return [
            'warnings'  => array_values(array_unique($warnings)),
            'forbidden' => $forbidden,
        ];
    }
}