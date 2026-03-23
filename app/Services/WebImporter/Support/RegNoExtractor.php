<?php

namespace App\Services\WebImporter\Support;

class RegNoExtractor
{
    private static array $patterns = [
        '/MET\.[A-Za-z]{2}\.[0-9\/]+/u',
        '/MET\s+[A-Z]{2}\s+[0-9\/]+/u',
        '/FIN[0-9\/]{3,}/u',
        '/CKC[A-Z0-9]{5,}/u',
        '/AKCSB[A-Z][0-9]{3,}/u',
        '/VDH[-A-Z0-9\/]{3,}/u',
        '/KUZ[0-9\/]{3,}/u',
    ];

    public static function extract(?string $text): ?string
    {
        if (!$text) {
            return null;
        }

        foreach (self::$patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return RegNoNormalizer::normalize($m[0]);
            }
        }

        return null;
    }
}