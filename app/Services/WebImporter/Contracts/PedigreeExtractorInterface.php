<?php

namespace App\Services\WebImporter\Contracts;

use App\Services\WebImporter\DTO\DogDto;

interface PedigreeExtractorInterface
{
    public function extract(string $html, DogDto $rootDog): DogDto;
}