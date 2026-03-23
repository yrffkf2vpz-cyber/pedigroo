<?php

namespace App\Services\Normalizers\Rules;

class SuffixRules
{
    /**
     * Multi-word kennel suffix patterns.
     */
    public static function list(): array
    {
        return [
            'vom wald',
            'of silvermoon',
            'del monte',
            'de la sierra',
            'von der burg',
        ];
    }
}