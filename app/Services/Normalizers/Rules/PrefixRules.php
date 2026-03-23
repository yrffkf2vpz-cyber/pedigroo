<?php

namespace App\Services\Normalizers\Rules;

class PrefixRules
{
    /**
     * Words that indicate kennel prefix.
     */
    public static function list(): array
    {
        return [
            'von', 'vom', 'van', 'de', 'di', 'du', 'of', 'from',
            'z', 'ze', 'des', 'del', 'la', 'le', 'the',
        ];
    }
}