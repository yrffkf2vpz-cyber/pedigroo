<?php

namespace App\Services\Normalizers;

class ChangeDetector
{
    /**
     * Változások felismerése.
     * Később: diff generálás, audit log.
     */
    public function detect(array $dog): array
    {
        return [
            'changes' => [],

            'debug' => [
                'input' => $dog,
            ],
        ];
    }
}