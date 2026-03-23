<?php

namespace App\Services\Normalizers\Rules;

class CleanupRules
{
    public static function accents(): array
    {
        return [
            'á' => 'a', 'é' => 'e', 'í' => 'i',
            'ó' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ú' => 'u', 'ü' => 'u', 'ű' => 'u',
        ];
    }

    public static function specialCharacters(): array
    {
        return [",", ".", "(", ")", "-", "/", "\\"];
    }
}