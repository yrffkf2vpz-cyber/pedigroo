<?php

namespace App\Dto\Pdf;

class PdfDocument
{
    public function __construct(
        public string $rawText,
        /** @var string[] */
        public array $pages,
        public array $metadata = [],
    ) {}
}
