<?php

namespace App\Services\Normalizers\Rules;

class OwnerRules
{
    public static function indicators(): array
    {
        return [
            'owner',
            'breeder',
            'handled by',
            'owned by',
        ];
    }
}