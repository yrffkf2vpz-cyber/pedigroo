<?php

namespace App\Services\Dog;

use App\Dto\RawDogData;
use App\Models\Dog;
use Illuminate\Support\Str;

class DogActivationService
{
    public function activate(RawDogData $raw): Dog
    {
        $dogId = $this->generateDogId($raw);

        $dog = Dog::firstOrNew(['dog_id' => $dogId]);

        $dog->dog_id       = $dogId;
        $dog->name         = $raw->name;
        $dog->reg_no       = $raw->reg_no;
        $dog->breed        = $raw->breed;
        $dog->sex          = $raw->sex;
        $dog->birth_date   = $raw->birth_date;
        $dog->kennel_name  = $raw->kennel_name;
        $dog->breeder_name = $raw->breeder_name;
        $dog->owner_name   = $raw->owner_name;
        $dog->source       = $raw->source;
        $dog->status       = $this->determineStatus($raw);

        $dog->save();

        return $dog;
    }

    protected function generateDogId(RawDogData $raw): string
    {
        if ($raw->reg_no) {
            $norm = strtoupper(preg_replace('/\s+/', '-', $raw->reg_no));
            return 'REG-' . $norm;
        }

        $slug = Str::slug($raw->name . '-' . ($raw->kennel_name ?? 'unknown'));
        return 'TEMP-' . strtoupper($slug);
    }

    protected function determineStatus(RawDogData $raw): string
    {
        if (! $raw->reg_no) {
            return 'temporary';
        }

        return 'active';
    }
}