<?php

namespace App\PipelineHandlers;

use App\Models\PipelineTask;
use App\Services\PedrooPipelineService;
use Illuminate\Support\Facades\Log;

class AiLearningPipelineHandler
{
    public function handle(PipelineTask $task)
    {
        try {
            // Payload kinyerése
            $payload = json_decode($task->payload, true);
            $breedId = $payload['breed_id'] ?? null;

            if (!$breedId) {
                throw new \Exception("breed_id hiányzik a payloadból.");
            }

            // AI pipeline futtatása
            $pipeline = app(PedrooPipelineService::class);
            $pipeline->runAiPipeline($breedId);

            // Task sikeres
            $task->status = 'success';
            $task->save();

            Log::info("AI learning pipeline sikeresen lefutott. Breed ID: {$breedId}");

        } catch (\Throwable $e) {

            // Task hibára futott
            $task->status = 'failed';
            $task->error_message = $e->getMessage();
            $task->save();

            Log::error("AI learning pipeline hiba: " . $e->getMessage());
        }
    }
}