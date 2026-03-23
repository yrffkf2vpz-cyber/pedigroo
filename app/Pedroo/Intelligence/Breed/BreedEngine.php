<?php

namespace App\Pedroo\Intelligence\Breed;

use App\Models\Dog;
use App\Models\PdBreedingRule;

class BreedEngine
{
    /**
     * Fajtaszabályok betöltése (rule_key => value)
     */
    public function loadRules(int $breedId): array
    {
        return PdBreedingRule::where('breed_id', $breedId)
            ->pluck('value', 'rule_key')
            ->toArray();
    }

    /**
     * Egy konkrét szabály értékének lekérése
     */
    public function getRuleValue(int $breedId, string $key): mixed
    {
        return PdBreedingRule::where('breed_id', $breedId)
            ->where('rule_key', $key)
            ->value('value');
    }

    /**
     * Kutya tenyészthetőségének ellenőrzése fajtaszabályok alapján
     *
     * Visszatér:
     *  [
     *      'min_breeding_age_male'      => true/false,
     *      'max_breeding_age_male'      => true/false,
     *      'breeding_exam_required'     => true/false,
     *      'health_tests_required'      => true/false,
     *      'min_months_between_litters' => true/false,
     *      ...
     *  ]
     */
    public function checkDogEligibility(Dog $dog): array
    {
        $rules = $this->loadRules($dog->breed_id);

        $results = [];

        // --- KOR SZABÁLYOK ---

        // Minimum kor (hónapban)
        if ($dog->sex === 'male' && isset($rules['min_breeding_age_male'])) {
            $results['min_breeding_age_male'] =
                $dog->ageInMonths() >= (int)$rules['min_breeding_age_male'];
        }

        if ($dog->sex === 'female' && isset($rules['min_breeding_age_female'])) {
            $results['min_breeding_age_female'] =
                $dog->ageInMonths() >= (int)$rules['min_breeding_age_female'];
        }

        // Maximum kor (hónapban)
        if ($dog->sex === 'male' && isset($rules['max_breeding_age_male'])) {
            $results['max_breeding_age_male'] =
                $dog->ageInMonths() <= (int)$rules['max_breeding_age_male'];
        }

        if ($dog->sex === 'female' && isset($rules['max_breeding_age_female'])) {
            $results['max_breeding_age_female'] =
                $dog->ageInMonths() <= (int)$rules['max_breeding_age_female'];
        }

        // --- VIZSGÁK / MINIMUM KÖVETELMÉNYEK ---

        // Kötelező tenyészvizsga
        if (isset($rules['breeding_exam_required'])) {
            $results['breeding_exam_required'] =
                (int)$rules['breeding_exam_required'] === 0
                || $dog->hasBreedingExam();
        }

        // Kötelező egészségügyi vizsgálatok (általános flag)
        if (isset($rules['health_tests_required'])) {
            $results['health_tests_required'] =
                (int)$rules['health_tests_required'] === 0
                || $dog->hasAllHealthTests();
        }

        // Kötelező munkavizsga
        if (isset($rules['working_exam_required'])) {
            $results['working_exam_required'] =
                (int)$rules['working_exam_required'] === 0
                || $dog->hasWorkingExam();
        }

        // Kötelező vadászvizsga
        if (isset($rules['hunting_exam_required'])) {
            $results['hunting_exam_required'] =
                (int)$rules['hunting_exam_required'] === 0
                || $dog->hasHuntingExam();
        }

        // Kötelező sportvizsga
        if (isset($rules['competition_exam_required'])) {
            $results['competition_exam_required'] =
                (int)$rules['competition_exam_required'] === 0
                || $dog->hasCompetitionExam();
        }

        // --- SZAPORODÁSI CIKLUS / ALMOK ---

        // Almok közti minimális pihenőidő (csak szukára)
        if ($dog->sex === 'female' && isset($rules['min_months_between_litters'])) {
            $lastLitter = $dog->litters()->latest()->first();

            if ($lastLitter) {
                $monthsSince = $lastLitter->created_at->diffInMonths(now());
                $results['min_months_between_litters'] =
                    $monthsSince >= (int)$rules['min_months_between_litters'];
            } else {
                // még nem volt alom → oké
                $results['min_months_between_litters'] = true;
            }
        }

        // Maximális alomszám (csak szukára)
        if ($dog->sex === 'female' && isset($rules['max_litters_per_female'])) {
            $results['max_litters_per_female'] =
                $dog->litters()->count() <= (int)$rules['max_litters_per_female'];
        }

        // --- SZÍN / MINTA SZABÁLYOK ---

        // Tiltott színek
        if (isset($rules['forbidden_colors']) && $rules['forbidden_colors'] !== null && $rules['forbidden_colors'] !== '') {
            $forbiddenColors = json_decode($rules['forbidden_colors'], true) ?: [];
            $results['forbidden_colors'] =
                !in_array($dog->color, $forbiddenColors, true);
        }

        // Tiltott minták
        if (isset($rules['forbidden_patterns']) && $rules['forbidden_patterns'] !== null && $rules['forbidden_patterns'] !== '') {
            $forbiddenPatterns = json_decode($rules['forbidden_patterns'], true) ?: [];
            $results['forbidden_patterns'] =
                !in_array($dog->pattern, $forbiddenPatterns, true);
        }

        // --- ÁLTALÁNOS / BŐVÍTHETŐ RÉSZ ---

        // Ide később további fajtaszabályok jöhetnek,
        // pl. minimális/ajánlott vizsgák, extra genetikai flag-ek stb.

        return $results;
    }
}