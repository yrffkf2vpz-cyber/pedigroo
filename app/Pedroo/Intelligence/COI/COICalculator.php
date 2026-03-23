<?php

namespace App\Pedroo\Intelligence\COI;

use App\Pedroo\Intelligence\COI\Models\PdDogAncestry;
use App\Pedroo\Intelligence\COI\Models\PdDogCoi;

class COICalculator
{
    /**
     * COI számítása egy kutyára (százalékban)
     */
    public function calculateForDog(int $dogId): float
    {
        // ősei az adott kutyának
        $ancestors = PdDogAncestry::where('dog_id', $dogId)->get();

        if ($ancestors->isEmpty()) {
            return 0.0;
        }

        // közös ősök keresése: ugyanaz az ancestor_id több úton
        $grouped = $ancestors->groupBy('ancestor_id');

        $coi = 0.0;

        foreach ($grouped as $ancestorId => $paths) {
            // ha csak egy út vezet az őshez, nincs inbreeding hozzájárulás
            if ($paths->count() < 2) {
                continue;
            }

            // ős COI-ja (FA)
            $FA = (float) (PdDogCoi::where('dog_id', $ancestorId)->value('coi') ?? 0.0) / 100.0;

            // minden lehetséges útpár (n1, n2)
            $pathsArray = $paths->values();

            for ($i = 0; $i < $pathsArray->count(); $i++) {
                for ($j = $i + 1; $j < $pathsArray->count(); $j++) {
                    $n1 = $pathsArray[$i]->generations;
                    $n2 = $pathsArray[$j]->generations;

                    $coi += pow(0.5, $n1 + $n2 + 1) * (1 + $FA);
                }
            }
        }

        return round($coi * 100, 4);
    }
}