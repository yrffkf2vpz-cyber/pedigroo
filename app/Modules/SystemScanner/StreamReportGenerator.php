<?php

namespace App\Modules\SystemScanner;

class StreamReportGenerator
{
    public function generate(
        array $modules,
        array $pipelines,
        array $commands,
        array $usage
    ): array {
        return [
            'summary' => [
                'module_count' => count($modules),
                'pipeline_count' => count($pipelines),
                'command_count' => count($commands),
                'total_runtime_hits' => array_sum($usage),
            ],
            'modules' => $modules,
            'pipelines' => $pipelines,
            'commands' => $commands,
        ];
    }
}
