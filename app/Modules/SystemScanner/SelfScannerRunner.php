<?php

namespace App\Modules\SystemScanner;

class SelfScannerRunner
{
    public function run(): array
    {
        /** @var SystemScannerService $scanner */
        $scanner = app(SystemScannerService::class);

        // 1) Statikus fájlrendszer beolvasása
        $fileMap = $scanner->scanFilesystem();

        // 2) Runtime használati adatok
        $usageMap = $scanner->usageMap ?? [];

        // 3) Modulok feltérképezése
        $moduleMap = app(ModuleMapBuilder::class)->build($fileMap, $usageMap);

        // 4) Pipeline-ok feltérképezése
        $pipelineMap = app(PipelineMapBuilder::class)->build($fileMap, $usageMap);

        // 5) Commandok feltérképezése
        $commandMap = app(CommandMapBuilder::class)->build($fileMap, $usageMap);

        // 6) Jelentés generálása (200 sor alatt)
        $report = app(SystemReportGenerator::class)->generate(
            $moduleMap,
            $pipelineMap,
            $commandMap,
            $fileMap,
            $usageMap
        );

        return $report;
    }
}
