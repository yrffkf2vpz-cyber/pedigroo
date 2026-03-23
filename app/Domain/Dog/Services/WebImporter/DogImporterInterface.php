<?php

namespace App\Services\WebImporter\Contracts;

use App\Services\WebImporter\DTO\DogDto;

interface DogImporterInterface
{
    public function import(DogDto $dog): int;
}