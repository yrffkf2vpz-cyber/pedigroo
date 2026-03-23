<?php

namespace App\Services\WebImporter;

use App\Services\WebImporter\Contracts\PageClassifierInterface;
use App\Services\WebImporter\Contracts\DogExtractorInterface;
use App\Services\WebImporter\Contracts\PedigreeExtractorInterface;
use App\Services\WebImporter\Contracts\NormalizerInterface;
use App\Services\WebImporter\Family\FamilyBuilder;

class WebImporterService
{
    public function __construct(
        private PageClassifierInterface $classifier,
        private DogExtractorInterface $dogExtractor,
        private PedigreeExtractorInterface $pedigreeExtractor,
        private NormalizerInterface $normalizer,
        private FamilyBuilder $familyBuilder,
    ) {}

    public function importFromHtml(string $html): int
    {
        $type = $this->classifier->classify($html);

        if ($type === 'dog') {
            $dog = $this->dogExtractor->extract($html);
            $dog = $this->normalizer->normalizeDog($dog);
            return $this->familyBuilder->build($dog);
        }

        if ($type === 'pedigree') {
            $dog = $this->dogExtractor->extract($html);
            $dog = $this->pedigreeExtractor->extract($html, $dog);
            $dog = $this->normalizer->normalizeDog($dog);
            return $this->familyBuilder->build($dog);
        }

        throw new \RuntimeException("Unsupported page type: $type");
    }
}