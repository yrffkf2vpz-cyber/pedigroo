<?php

namespace App\Services\Normalizers\Rules;

class KennelRules
{
    /**
     * Complex kennel name patterns.
     */
    public static function complex(): array
    {
        return [
            'von der',
            'de la',
            'del monte',
            'of the',
        ];
    }
}