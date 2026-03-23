<?php

namespace App\Dto\Pdf;

class NormalizedPdfItem
{
    public function __construct(
        public ?string $dogName,
        public ?string $regNoRaw,
        public ?string $chip,
        public ?string $breedRaw,
        public ?string $resultRaw,
        public ?string $judgeRaw,
        public ?\DateTimeInterface $date,
        public array $raw = [],
    ) {}
}
