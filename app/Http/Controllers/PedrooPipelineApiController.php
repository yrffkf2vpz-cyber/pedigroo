<?php

namespace App\Http\Controllers;

use App\Services\Pedroo\PipelineAnalyzer;

class PedrooPipelineApiController extends Controller
{
    public function show(string $pipeline, PipelineAnalyzer $analyzer)
    {
        // Pipeline mappa helye
        $directory = base_path("app/{$pipeline}");

        if (!is_dir($directory)) {
            return response()->json([
                'pipeline' => $pipeline,
                'status' => 'red',
                'reason' => 'Pipeline directory not found',
                'files' => []
            ], 404);
        }

        // Pipeline elemzése
        $result = $analyzer->analyze($pipeline, $directory);

        return response()->json($result);
    }
}