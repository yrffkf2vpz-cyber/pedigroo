<?php

namespace App\Services\Ingest\Pdf;

use App\Dto\Pdf\PdfDocument;
use App\Dto\Pdf\NormalizedPdfItem;

interface PdfNormalizerInterface
{
    /**
     * @return NormalizedPdfItem[]
     */
    public function normalize(PdfDocument $document): array;
}
