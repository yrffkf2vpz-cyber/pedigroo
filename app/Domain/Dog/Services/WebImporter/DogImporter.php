<?php

namespace App\Services\WebImporter\Importer;

use App\Models\Dog;
use App\Services\WebImporter\Contracts\DogImporterInterface;
use App\Services\WebImporter\DTO\DogDto;

class DogImporter implements DogImporterInterface
{
    public function import(DogDto $dto): int
    {
        $dog = Dog::firstOrNew(['reg_no' => $dto->regNo]);

        $dog->name = $dto->name;
        $dog->breed = $dto->breed;
        $dog->sex = $dto->sex;
        $dog->dob = $dto->dob;

        $dog->save();

        return $dog->id;
    }
}