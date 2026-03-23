<?php

namespace App\Services\Pedroo;

class PipelineAnalyzer
{
    private RulesEngine $rulesEngine;

    public function __construct(RulesEngine $rulesEngine)
    {
        $this->rulesEngine = $rulesEngine;
    }

    public function analyze(string $pipeline, string $directory): array
    {
        $rules = $this->rulesEngine->getRulesForPipeline($pipeline);

        if (!$rules) {
            return [
                'pipeline' => $pipeline,
                'status' => 'red',
                'reason' => 'Pipeline not found in rules.json',
                'files' => []
            ];
        }

        // 1) Fájlok beolvasása a mappából
        $existingFiles = $this->scanDirectory($directory);

        // 2) Kötelezo fájlok ellenorzése
        $missingFiles = array_diff($rules['required'], $existingFiles);

        // 3) Felesleges fájlok
        $unexpectedFiles = array_diff($existingFiles, array_merge(
            $rules['required'],
            $rules['optional'],
            $rules['forbidden']
        ));

        $results = [];

        // 4) Létezo fájlok értékelése
        foreach ($existingFiles as $fileName) {
            $content = file_get_contents($directory . '/' . $fileName);

            $evaluation = $this->rulesEngine->evaluateFile(
                $pipeline,
                $fileName,
                $content
            );

            $results[] = [
                'file' => $fileName,
                'status' => $evaluation['status'],
                'reason' => $evaluation['reason']
            ];
        }

        // 5) Hiányzó fájlok (blue)
        foreach ($missingFiles as $fileName) {
            $results[] = [
                'file' => $fileName,
                'status' => 'blue',
                'reason' => 'Missing required file'
            ];
        }

        // 6) Felesleges fájlok (red)
        foreach ($unexpectedFiles as $fileName) {
            $results[] = [
                'file' => $fileName,
                'status' => 'red',
                'reason' => 'Unexpected file in pipeline'
            ];
        }

        // 7) Pipeline össz-státusz meghatározása
        $pipelineStatus = $this->calculatePipelineStatus($results);

        return [
            'pipeline' => $pipeline,
            'status' => $pipelineStatus,
            'files' => $results
        ];
    }

    private function scanDirectory(string $directory): array
    {
        return array_map('basename', glob($directory . '/*.php'));
    }

    private function calculatePipelineStatus(array $results): string
    {
        $statuses = array_column($results, 'status');

        if (in_array('red', $statuses)) return 'red';
        if (in_array('blue', $statuses)) return 'blue';
        if (in_array('yellow', $statuses)) return 'yellow';

        return 'green';
    }
}