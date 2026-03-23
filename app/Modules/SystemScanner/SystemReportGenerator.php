<?php

namespace App\Modules\SystemScanner;

class SystemReportGenerator
{
    public function generate(
        array $moduleMap,
        array $pipelineMap,
        array $commandMap,
        array $fileMap,
        array $usageMap
    ): array {
        return [
            'summary' => $this->buildSummary($moduleMap, $pipelineMap, $commandMap),
            'modules' => $this->compressModules($moduleMap),
            'pipelines' => $this->compressPipelines($pipelineMap),
            'commands' => $this->compressCommands($commandMap),
            'stats' => $this->buildStats($fileMap, $usageMap),
        ];
    }

    /**
     * 1) Felso szintu összefoglaló.
     */
    protected function buildSummary(array $modules, array $pipelines, array $commands): array
    {
        return [
            'module_count' => count($modules),
            'pipeline_count' => count($pipelines),
            'command_count' => count($commands),

            'active_modules' => count(array_filter($modules, fn($m) => $m['status'] === 'active')),
            'active_pipelines' => count(array_filter($pipelines, fn($p) => $p['status'] === 'active')),
            'active_commands' => count(array_filter($commands, fn($c) => $c['status'] === 'active')),

            'unused_modules' => count(array_filter($modules, fn($m) => $m['status'] === 'possibly_unused')),
            'unused_pipelines' => count(array_filter($pipelines, fn($p) => $p['status'] === 'possibly_unused')),
            'unused_commands' => count(array_filter($commands, fn($c) => $c['status'] === 'possibly_unused')),
        ];
    }

    /**
     * 2) Modulok tömörítése.
     */
    protected function compressModules(array $modules): array
    {
        $result = [];

        foreach ($modules as $name => $m) {
            $result[$name] = [
                'status' => $m['status'],
                'usage' => $m['usage'],
                'file_count' => count($m['files']),
                'dependency_count' => count($m['dependencies']),
            ];
        }

        return $result;
    }

    /**
     * 3) Pipeline-ok tömörítése.
     */
    protected function compressPipelines(array $pipelines): array
    {
        $result = [];

        foreach ($pipelines as $name => $p) {
            $result[$name] = [
                'status' => $p['status'],
                'usage' => $p['usage'],
                'task_count' => $p['task_count'],
                'dependency_count' => $p['dependency_count'],
                'category' => $p['category'],
            ];
        }

        return $result;
    }

    /**
     * 4) Commandok tömörítése.
     */
    protected function compressCommands(array $commands): array
    {
        $result = [];

        foreach ($commands as $name => $c) {
            $result[$name] = [
                'status' => $c['status'],
                'usage' => $c['usage'],
                'dependency_count' => $c['dependency_count'],
                'category' => $c['category'],
            ];
        }

        return $result;
    }

    /**
     * 5) Általános statisztikák.
     */
    protected function buildStats(array $fileMap, array $usageMap): array
    {
        return [
            'total_files' => count($fileMap),
            'total_runtime_hits' => array_sum($usageMap),
            'unused_files' => count(array_filter($fileMap, fn($f) => !isset($usageMap[$f['path']]))),
        ];
    }
}
