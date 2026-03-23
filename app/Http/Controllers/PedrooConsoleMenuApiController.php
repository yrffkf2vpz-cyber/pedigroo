<?php

namespace App\Http\Controllers;

use App\Services\Pedroo\PipelineAnalyzer;

class PedrooConsoleMenuApiController extends Controller
{
    public function index(PipelineAnalyzer $analyzer)
    {
        // Pipeline lista – késobb automatikusan generálható
        $pipelines = ['dogs', 'breeds', 'events', 'owners'];

        $menu = [];

        foreach ($pipelines as $pipeline) {

            $directory = base_path("app/" . ucfirst($pipeline));

            if (!is_dir($directory)) {
                $menu[] = [
                    'pipeline' => $pipeline,
                    'status' => 'red',
                    'reason' => 'Pipeline directory missing'
                ];
                continue;
            }

            $result = $analyzer->analyze($pipeline, $directory);

            $menu[] = [
                'pipeline' => $pipeline,
                'status' => $result['status'],
                'reason' => $result['status'] === 'green'
                    ? 'OK'
                    : 'Has issues'
            ];
        }

        return response()->json($menu);
    }
}