<?php

namespace App\Services\Import;

use App\Models\PedrooDog;
use App\Events\DogImported;

class DogImportService
{
    public function import(array $rawDog): void
    {
        // 1) ESEMÉNY MEGHÍVÁSA — EZ KELL NEKÜNK
        DogImported::dispatch($rawDog);

        // 2) A nyers kutya mentése a sandboxba
        PedrooDog::create([
            'reg_no'  => $rawDog['reg_no'] ?? null,
            'name'    => $rawDog['name'] ?? null,
            'color'   => $rawDog['color'] ?? null,
            'country' => $rawDog['country'] ?? null,
            'health'  => $rawDog['health'] ?? [],
        ]);
    }
}