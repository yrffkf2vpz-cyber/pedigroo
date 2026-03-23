<?php

namespace App\Services\Ingest;

use App\Models\Ingest\PdfImport;
use App\Services\Ingest\Pdf\PdfTextExtractor;
use App\Services\Ingest\Pdf\PdfNormalizerFactory;
use App\Services\Ingest\Builders\DogRecordBuilder;
use App\Services\Ingest\Savers\SandboxDogSaver;
use App\Events\HealthRecordImported;
use App\Events\EventResultImported;

class IngestPdfService
{
    public function __construct(
        private PdfTextExtractor $extractor,
        private PdfNormalizerFactory $normalizers,
        private DogRecordBuilder $dogBuilder,
        private SandboxDogSaver $sandboxSaver,
    ) {}

    public function ingest(PdfImport $import): array
    {
        $document   = $this->extractor->extract(storage_path('app/'.$import->file_path));
        $normalizer = $this->normalizers->forType($import->type);

        $items = $normalizer->normalize($document);

        $stats = [
            'total'   => count($items),
            'created' => 0,
            'updated' => 0,
            'errors'  => 0,
        ];

        foreach ($items as $item) {
            try {
                $dogRecord = $this->dogBuilder->fromPdfItem($item);

                $result = $this->sandboxSaver->save($dogRecord);
                // result lehet pl. ['action' => 'created'|'updated']

                if (($result['action'] ?? null) === 'created') {
                    $stats['created']++;
                } elseif (($result['action'] ?? null) === 'updated') {
                    $stats['updated']++;
                }

                // Event típus szerint
                if ($import->type === 'health') {
                    event(new HealthRecordImported($dogRecord));
                } elseif ($import->type === 'event') {
                    event(new EventResultImported($dogRecord));
                }
            } catch (\Throwable $e) {
                $stats['errors']++;
                $import->appendLog('Row error', [
                    'message' => $e->getMessage(),
                    'item'    => $item->raw ?? [],
                ]);
            }
        }

        return $stats;
    }
}
