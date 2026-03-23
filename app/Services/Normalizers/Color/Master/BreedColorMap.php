<?php

namespace App\Services\Normalizers\Color\Master;

class BreedColorMap
{
    public static function map(): array
    {
        return [
            'kuvasz' => [
                'white', // csak fehér
            ],
            'mudi' => [
                'black',
                'brown',
                'white',
                'grey',
                'fawn',
                'merle', // késobb külön kezelheto
            ],
            'magyar_vizsla' => [
                'golden',
                'rust',
            ],
            // késobb bovül
        ];
    }
}