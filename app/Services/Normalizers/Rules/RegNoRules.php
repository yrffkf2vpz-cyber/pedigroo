<?php

namespace App\Services\Normalizers\Rules;

class RegNoRules
{
    /**
     * Regex patterns for registration numbers.
     */
    public static function patterns(): array
    {
        return [
            '/([0-9]{1,6}[A-Z]?(?:\/[A-Z0-9]{1,4}))/i',
            '/([A-Z]{2,4}[0-9]{3,6})/i',
        ];
    }
}