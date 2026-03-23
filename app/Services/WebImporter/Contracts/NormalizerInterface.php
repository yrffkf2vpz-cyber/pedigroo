<?php

namespace App\Services\WebImporter\Contracts;

use App\Services\WebImporter\DTO\DogDto;

interface NormalizerInterface
{
    public function normalizeDog(DogDto $dog): DogDto;
}