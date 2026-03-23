<?php

namespace App\Services\Normalizers;

class ChampionPointCalculator
{
    /**
     * Champion pontok számítása.
     * Később: országonkénti szabályok, CAC/CACIB pontok, rangsorok.
     */
    public function calculate(array $results): array
    {
        return [
            'points' => 0,

            'debug' => [
                'input'      => $results,
                'calculated' => 0,
            ],
        ];
    }
}