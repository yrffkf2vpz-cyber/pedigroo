<?php

namespace App\Services\WebImporter\Contracts;

use App\Services\WebImporter\DTO\DogDto;

interface DogExtractorInterface
{
    public function extract(string $html): DogDto;
}