<?php

namespace App\Services\Ingest\Pdf;

use Smalot\PdfParser\Parser;
use App\Dto\Pdf\PdfDocument;

class PdfTextExtractor
{
    public function __construct(
        private ?Parser $parser = null,
    ) {
        $this->parser ??= new Parser();
    }

    public function extract(string $filePath): PdfDocument
    {
        $pdf  = $this->parser->parseFile($filePath);
        $text = $pdf->getText();

        $pages = explode("\f", $text);
        $meta  = $pdf->getDetails() ?? [];

        return new PdfDocument(
            rawText: $text,
            pages: $pages,
            metadata: $meta
        );
    }
}
