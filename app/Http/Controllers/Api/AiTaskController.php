<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PedrooPipelineService;
use Illuminate\Http\Request;

class AiTaskController extends Controller
{
    public function handle(Request $request)
    {
        $task    = $request->input('task');
        $payload = $request->input('payload');

        // Payload parse
        $data = $payload ? json_decode($payload, true) : [];

        try {
            switch ($task) {

                /**
                 * AI tanulási pipeline
                 * ? PedrooPipelineService::runAiPipeline()
                 */
                case 'ai:learning':
                    return $this->runAiLearning($data);

                default:
                    return response()->json([
                        'error'   => 'Unknown AI task',
                        'task'    => $task,
                        'payload' => $data,
                    ], 400);
            }

        } catch (\Throwable $e) {

            return response()->json([
                'error'   => $e->getMessage(),
                'task'    => $task,
                'payload' => $data,
            ], 500);
        }
    }

    /**
     * AI tanulási pipeline futtatása
     * ? PatternMiner ? RuleGenerator ? RuleSuggestion (mentés)
     * ? mindezt a PedrooPipelineService végzi
     */
    private function runAiLearning(array $data)
    {
        $breedId = $data['breed_id'] ?? null;

        if (!$breedId) {
            return response()->json([
                'error' => 'breed_id missing in payload'
            ], 400);
        }

        // Teljes AI pipeline futtatása
        $result = app(PedrooPipelineService::class)->runAiPipeline($breedId);

        return response()->json([
            'result' => $result
        ]);
    }
}