<?php

namespace App\Services\Ingest\Pdf;

use App\Services\Ingest\Pdf\Normalizers\PdfNormalizerInterface;
use App\Services\Ingest\Pdf\Normalizers\HealthPdfNormalizer;
use App\Services\Ingest\Pdf\Normalizers\EventPdfNormalizer;
use App\Services\Ingest\Pdf\Normalizers\PedigreePdfNormalizer;

class PdfNormalizerFactory
{
    public function forType(string $type): PdfNormalizerInterface
    {
        return match ($type) {
            'health'   => app(HealthPdfNormalizer::class),
            'event'    => app(EventPdfNormalizer::class),
            'pedigree' => app(PedigreePdfNormalizer::class),
            default    => throw new \InvalidArgumentException("Ismeretlen PDF import típus: {$type}"),
        };
    }
}
