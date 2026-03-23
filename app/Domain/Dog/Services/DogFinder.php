<?php

namespace App\Services\Dog;

use App\Dto\RawDogData;

interface DogFinder
{
    public function findByName(string $name): ?RawDogData;
}