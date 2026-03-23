<?php

namespace App\Jobs;

use App\Models\Ingest\PdfImport;
use App\Services\Ingest\IngestPdfService;

class ProcessPdfImportJob extends Job
{
    public function __construct(
        public int $importId,
    ) {}

    public function handle(IngestPdfService $service): void
    {
        $import = PdfImport::findOrFail($this->importId);

        $import->markRunning();

        try {
            $stats = $service->ingest($import);
            $import->markDone($stats);
        } catch (\Throwable $e) {
            $import->markFailed($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
