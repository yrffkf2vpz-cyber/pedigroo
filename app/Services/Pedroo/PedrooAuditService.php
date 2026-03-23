<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class PedrooAuditService
{
    public function run(): array
    {
        $expected = config('pedroo_expected');

        $results = [
            'missing' => [],
            'extra' => [],
            'mismatch' => [],
            'ok' => [],
        ];

        /*
        |--------------------------------------------------------------------------
        | FILE AUDIT
        |--------------------------------------------------------------------------
        */
        $expectedFiles = $expected['files'] ?? [];
        $registryFiles = DB::table('pd_pedroo_registry')
            ->where('entity_type', 'file')
            ->pluck('entity_name')
            ->toArray();

        $registryFilesSet = array_flip($registryFiles);

        // Missing
        foreach ($expectedFiles as $file) {
            if (!isset($registryFilesSet[$file])) {
                $results['missing'][] = ['type' => 'file', 'name' => $file];
            } else {
                $results['ok'][] = ['type' => 'file', 'name' => $file];
            }
        }

        // Extra
        foreach ($registryFiles as $file) {
            if (!in_array($file, $expectedFiles, true)) {
                $results['extra'][] = ['type' => 'file', 'name' => $file];
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PIPELINE AUDIT
        |--------------------------------------------------------------------------
        */
        $expectedSteps = $expected['pipeline_steps'] ?? [];

        foreach ($expectedSteps as $module => $steps) {
            $registrySteps = DB::table('pd_pedroo_registry')
                ->where('entity_type', 'pipeline_step')
                ->where('module', $module)
                ->pluck('entity_name')
                ->toArray();

            $registryStepsSet = array_flip($registrySteps);

            // Missing
            foreach ($steps as $step) {
                if (!isset($registryStepsSet[$step])) {
                    $results['missing'][] = [
                        'type' => 'pipeline_step',
                        'module' => $module,
                        'name' => $step
                    ];
                } else {
                    $results['ok'][] = [
                        'type' => 'pipeline_step',
                        'module' => $module,
                        'name' => $step
                    ];
                }
            }

            // Extra
            foreach ($registrySteps as $step) {
                if (!in_array($step, $steps, true)) {
                    $results['extra'][] = [
                        'type' => 'pipeline_step',
                        'module' => $module,
                        'name' => $step
                    ];
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TABLE AUDIT
        |--------------------------------------------------------------------------
        */
        $expectedTables = $expected['tables'] ?? [];
        $registryTables = DB::table('pd_pedroo_registry')
            ->where('entity_type', 'table')
            ->pluck('entity_name')
            ->toArray();

        $registryTablesSet = array_flip($registryTables);

        // Missing
        foreach ($expectedTables as $table => $meta) {
            if (!isset($registryTablesSet[$table])) {
                $results['missing'][] = ['type' => 'table', 'name' => $table];
            } else {
                $results['ok'][] = ['type' => 'table', 'name' => $table];
            }
        }

        // Extra
        foreach ($registryTables as $table) {
            if (!array_key_exists($table, $expectedTables)) {
                $results['extra'][] = ['type' => 'table', 'name' => $table];
            }
        }

        return $results;
    }
}