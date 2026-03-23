<?php

namespace App\Services\WebImporter\Support;

class RegNoNormalizer
{
    public static function normalize(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        $raw = trim($raw);
        $raw = preg_replace('/\s+/', '', $raw); // whitespace eltávolítás

        // Magyar MET
        if (preg_match('/^MET\.[A-Za-z]{2}\.[0-9\/]+$/u', $raw)) {
            return strtoupper($raw);
        }

        // Magyar vizsla MET MV
        if (preg_match('/^METMV[0-9\/]+$/u', $raw)) {
            return strtoupper($raw);
        }

        // Finn FIN
        if (preg_match('/^FIN[0-9\/]+$/u', $raw)) {
            return strtoupper($raw);
        }

        // Kanada CKC
        if (preg_match('/^CKC[A-Z0-9]+$/u', $raw)) {
            return strtoupper($raw);
        }

        // Amerikai AKCSB
        if (preg_match('/^AKCSB[A-Z][0-9]+$/u', $raw)) {
            return strtoupper($raw);
        }

        // Német VDH
        if (preg_match('/^VDH[A-Z0-9\/\-]+$/u', $raw)) {
            return strtoupper($raw);
        }

        // Szerb KUZ
        if (preg_match('/^KUZ[0-9\/]+$/u', $raw)) {
            return strtoupper($raw);
        }

        // fallback
        return strtoupper($raw);
    }
}