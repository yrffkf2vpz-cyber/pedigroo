<?php

namespace App\Services\WebImporter\Normalizer;

use App\Services\WebImporter\Contracts\NormalizerInterface;
use App\Services\WebImporter\DTO\DogDto;
use App\Services\WebImporter\Support\HungarianDateParser;

class GenericNormalizer implements NormalizerInterface
{
    public function normalizeDog(DogDto $dog): DogDto
    {
        $dog->regNo = $this->normalizeRegNo($dog->regNo);
        $dog->dob   = $this->normalizeDate($dog->dob);
        $dog->name  = $this->normalizeName($dog->name);
        $dog->breed = $this->normalizeName($dog->breed);
        $dog->kennel= $this->normalizeName($dog->kennel);
        $dog->breeder = $this->normalizeName($dog->breeder);
        $dog->owner   = $this->normalizeName($dog->owner);

        if ($dog->sire) {
            $dog->sire = $this->normalizeDog($dog->sire);
        }
        if ($dog->dam) {
            $dog->dam = $this->normalizeDog($dog->dam);
        }

        return $dog;
    }

    private function normalizeDate(?string $date): ?string
    {
        if (!$date) {
            return null;
        }

        // ha már ISO formátum, hagyjuk
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        return HungarianDateParser::parse($date);
    }

    private function normalizeRegNo(?string $regNo): ?string
    {
        if (!$regNo) {
            return null;
        }

        $regNo = trim($regNo);
        $regNo = preg_replace('/\s+/', ' ', $regNo);

        return $regNo;
    }

    private function normalizeName(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        $name = trim($name);
        $name = preg_replace('/\s+/', ' ', $name);

        return $name;
    }
}