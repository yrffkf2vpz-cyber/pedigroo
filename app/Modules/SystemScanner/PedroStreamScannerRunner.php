<?php

namespace App\Modules\SystemScanner;

class PedroStreamScannerRunner
{
    public function run(): array
    {
        $scanner   = app(PedroStreamScanner::class);
        $usage     = app(StreamUsageTracker::class);
        $modules   = app(StreamModuleAggregator::class);
        $pipelines = app(StreamPipelineAggregator::class);
        $commands  = app(StreamCommandAggregator::class);

        $scanner->streamFiles(function ($path) use ($modules, $pipelines, $commands) {
            $content = @file_get_contents($path);
            if ($content === false) {
                return;
            }

            preg_match_all('/use\s+([^;]+);/', $content, $matches);
            $uses = $matches[1] ?? [];

            $modules->processFile($path, $uses);
            $pipelines->processFile($path, $uses);
            $commands->processFile($path, $uses);
        });

        $usageMap = $usage->getAll();

        return app(StreamReportGenerator::class)->generate(
            $modules->modules,
            $pipelines->pipelines,
            $commands->commands,
            $usageMap
        );
    }
}

