<?php

namespace App\Services\Normalizers;

class DuplicateResolver
{
    /**
     * Duplikációk felismerése.
     * Később: automatikus összeolvasztás.
     */
    public function resolve(array $dog): array
    {
        return [
            'duplicates' => [],

            'debug' => [
                'input' => $dog,
            ],
        ];
    }
}