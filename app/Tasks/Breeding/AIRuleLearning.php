<?php

namespace App\Tasks\Breeding;

use App\Models\Dog;
use App\Services\AI\PatternMinerService;
use App\Services\AI\RuleGeneratorService;
use App\Services\AI\RuleSuggestionService;
use App\Services\FuzzyNormalizerService;

class AIRuleLearning
{
    public function handle(int $breedId): array
    {
        // 1) Kutya adatok lekérése
        $dogs = Dog::where('breed_id', $breedId)->get();

        if ($dogs->isEmpty()) {
            return ['status' => 'no_dogs'];
        }

        // 2) Normalizálás
        $normalizer = app(FuzzyNormalizerService::class);
        $normalized = $dogs->map(fn($dog) => $normalizer->normalize($dog->toArray()))->toArray();

        // 3) Mintakeresés
        $patterns = app(PatternMinerService::class)->mine($normalized);

        // 4) Szabálygenerálás
        $rules = app(RuleGeneratorService::class)->generate($patterns);

        // 5) Mentés
        app(RuleSuggestionService::class)->store($breedId, $rules);

        return [
            'status' => 'ok',
            'dogs_processed' => count($normalized),
            'rules_generated' => count($rules),
        ];
    }
}