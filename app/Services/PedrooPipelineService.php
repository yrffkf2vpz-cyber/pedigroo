<?php

namespace App\Services;

use App\Models\PipelineTask;
use App\Services\RuleEngine\RuleEngineService;

class PedrooPipelineService
{
    public function __construct(
        protected RuleEngineService $ruleEngine,
        // többi engine...
    ) {}

    public function evaluateDog(Dog $dog): array
    {
        return [
            // 'color'  => $this->colorEngine->evaluate($dog),
            // 'breed'  => $this->breedEngine->evaluate($dog),
            // 'health' => $this->healthEngine->evaluate($dog),
            // 'coi'    => $this->coiEngine->evaluate($dog),
            'rules'  => $this->ruleEngine->evaluateDog($dog),
        ];
    }
}



    public function status(): array
    {
        $running = PipelineTask::where('status', 'pending')->exists();

        return [
            'status' => $running ? 'running' : 'idle',
        ];
    }

    public function runMasterPlan(): string
    {
        app(\App\Http\Controllers\PipelineController::class)->run(request());

        return 'Master Plan futtatása elindítva.';
    }

    /**
     * AI tanulási pipeline indítása egy konkrét fajtára
     */
    public function runAiLearning(int $breedId): string
    {
        PipelineTask::create([
            'type'    => 'ai:learning',                         // konzisztens Pedroo task-név
            'payload' => json_encode(['breed_id' => $breedId]), // AI oldalon parse-oljuk
            'status'  => 'pending',
            'log'     => null,
        ]);

        return "AI tanulási pipeline elindítva a(z) {$breedId} fajtára.";
    }
    public function runAiPipeline(int $breedId = null): array
{
    // 1) Minták kinyerése a LearningQueue-ból
    $patterns = app(\App\Services\AI\PatternMinerService::class)->mine();

    // 2) Szabályjavaslatok generálása
    $rules = app(\App\Services\AI\RuleGeneratorService::class)->generate($patterns);

    // 3) Szabályjavaslatok mentése adatbázisba
    app(\App\Services\AI\RuleSuggestionService::class)->saveSuggestions($rules, $breedId);

    // 4) Visszatérés a teljes pipeline eredménnyel
    return [
        'breed_id' => $breedId,
        'patterns' => $patterns,
        'rules'    => $rules,
        'saved'    => true,
    ];
}
}
