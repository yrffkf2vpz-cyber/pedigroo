<?php

namespace App\Services\Registration;

/**
 * A Pedroo központi regisztrációs szám normalizálója.
 *
 * Feladata:
 * - országonként felismerni a reg_no formátumokat
 * - történeti változásokat kezelni (pl. FIN ? FI)
 * - hibás formákat javítani
 * - a magyar fajtakódokat kisbetusen visszaadni (Ku, Mu, Mv)
 * - egységes Pedroo-formátumot eloállítani
 *
 * A cél: stabil, duplikációmentes, tanulható adatmodell.
 */
class RegistrationNumberService
{
    public static function normalize(?string $raw): ?string
    {
        if (!$raw) {
            return null;
        }

        // alap tisztítás
        $raw = trim($raw);
        $raw = preg_replace('/\s+/', '', $raw); // whitespace eltávolítás
        $raw = str_replace(['–','—'], '-', $raw); // hosszú kötojelek normalizálása
        $raw = strtoupper($raw); // országkódok nagybetusek

        // ---------------------------------------------------------
        // MAGYAR MET – fajtakód kisbetus (Ku, Mu, Mv)
        // ---------------------------------------------------------

        // Kuvasz
        if (self::isMetKu($raw)) {
            return self::normalizeMetKu($raw);
        }

        // Mudi
        if (self::isMetMu($raw)) {
            return self::normalizeMetMu($raw);
        }

        // Magyar vizsla
        if (self::isMetMv($raw)) {
            return self::normalizeMetMv($raw);
        }

        // ---------------------------------------------------------
        // FINN – régi FIN ? új FI
        // ---------------------------------------------------------

        if (self::isOldFin($raw)) {
            return self::normalizeOldFin($raw);
        }

        if (self::isNewFi($raw)) {
            return self::normalizeNewFi($raw);
        }

        // ---------------------------------------------------------
        // CKC – Kanada
        // ---------------------------------------------------------

        if (self::isCkc($raw)) {
            return self::normalizeCkc($raw);
        }

        // ---------------------------------------------------------
        // AKCSB – USA
        // ---------------------------------------------------------

        if (self::isAkcsb($raw)) {
            return self::normalizeAkcsb($raw);
        }

        // ---------------------------------------------------------
        // VDH – Németország
        // ---------------------------------------------------------

        if (self::isVdh($raw)) {
            return self::normalizeVdh($raw);
        }

        // ---------------------------------------------------------
        // KUZ – Szerbia
        // ---------------------------------------------------------

        if (self::isKuz($raw)) {
            return self::normalizeKuz($raw);
        }

        // fallback – ha nem ismert formátum
        return $raw;
    }

    // ============================================================
    // MAGYAR MET – Kuvasz (MET.Ku.)
    // ============================================================

    private static function isMetKu(string $raw): bool
    {
        return (bool) preg_match('/^MET\.?KU[0-9\/\.]*$/i', $raw);
    }

    private static function normalizeMetKu(string $raw): string
    {
        // minden formátum ? MET.Ku.xxxx/yy
        $raw = preg_replace('/^METKU/i', 'MET.Ku.', $raw);
        $raw = preg_replace('/^MET\.KU/i', 'MET.Ku.', $raw);
        $raw = preg_replace('/^MET-KU/i', 'MET.Ku.', $raw);
        $raw = preg_replace('/^MET KU/i', 'MET.Ku.', $raw);

        // ha hiányzik a pont a Ku után
        if (!preg_match('/^MET\.Ku\./', $raw)) {
            $raw = preg_replace('/^MET\.Ku/', 'MET.Ku.', $raw);
        }

        return $raw;
    }

    // ============================================================
    // MAGYAR MET – Mudi (MET.Mu.)
    // ============================================================

    private static function isMetMu(string $raw): bool
    {
        return (bool) preg_match('/^MET\.?MU[0-9\/\.]*$/i', $raw);
    }

    private static function normalizeMetMu(string $raw): string
    {
        $raw = preg_replace('/^METMU/i', 'MET.Mu.', $raw);
        $raw = preg_replace('/^MET\.MU/i', 'MET.Mu.', $raw);
        $raw = preg_replace('/^MET-MU/i', 'MET.Mu.', $raw);
        $raw = preg_replace('/^MET MU/i', 'MET.Mu.', $raw);

        if (!preg_match('/^MET\.Mu\./', $raw)) {
            $raw = preg_replace('/^MET\.Mu/', 'MET.Mu.', $raw);
        }

        return $raw;
    }

    // ============================================================
    // MAGYAR MET – Magyar vizsla (MET.Mv.)
    // ============================================================

    private static function isMetMv(string $raw): bool
    {
        return (bool) preg_match('/^MET\.?MV[0-9\/\.]*$/i', $raw);
    }

    private static function normalizeMetMv(string $raw): string
    {
        $raw = preg_replace('/^METMV/i', 'MET.Mv.', $raw);
        $raw = preg_replace('/^MET\.MV/i', 'MET.Mv.', $raw);
        $raw = preg_replace('/^MET-MV/i', 'MET.Mv.', $raw);
        $raw = preg_replace('/^MET MV/i', 'MET.Mv.', $raw);

        if (!preg_match('/^MET\.Mv\./', $raw)) {
            $raw = preg_replace('/^MET\.Mv/', 'MET.Mv.', $raw);
        }

        return $raw;
    }

    // ============================================================
    // FINN – régi FIN ? FI.FIN.
    // ============================================================

    private static function isOldFin(string $raw): bool
    {
        return (bool) preg_match('/^FIN[0-9\/]+$/', $raw);
    }

    private static function normalizeOldFin(string $raw): string
    {
        // FIN47665/03 ? FI.FIN.47665/03
        return preg_replace('/^FIN/', 'FI.FIN.', $raw);
    }

    // ============================================================
    // FINN – új FI formátum
    // ============================================================

    private static function isNewFi(string $raw): bool
    {
        return (bool) preg_match('/^FI[A-Z0-9\-\/]+$/', $raw);
    }

    private static function normalizeNewFi(string $raw): string
    {
        return $raw;
    }

    // ============================================================
    // CKC – Kanada
    // ============================================================

    private static function isCkc(string $raw): bool
    {
        return (bool) preg_match('/^CKC[A-Z0-9]+$/', $raw);
    }

    private static function normalizeCkc(string $raw): string
    {
        // CKCSTB1011416 ? CKC.STB.1011416
        if (preg_match('/^CKC([A-Z]{3})([0-9]+)/', $raw, $m)) {
            return sprintf('CKC.%s.%s', $m[1], $m[2]);
        }

        return $raw;
    }

    // ============================================================
    // AKCSB – USA
    // ============================================================

    private static function isAkcsb(string $raw): bool
    {
        return (bool) preg_match('/^AKCSB[A-Z][0-9]+$/', $raw);
    }

    private static function normalizeAkcsb(string $raw): string
    {
        return $raw;
    }

    // ============================================================
    // VDH – Németország
    // ============================================================

    private static function isVdh(string $raw): bool
    {
        return (bool) preg_match('/^VDH[A-Z0-9\-\/]+$/', $raw);
    }

    private static function normalizeVdh(string $raw): string
    {
        // VDH-KVD494 ? VDH.KVD.494
        if (preg_match('/^VDH-([A-Z]{3})([0-9]+)/', $raw, $m)) {
            return sprintf('VDH.%s.%s', $m[1], $m[2]);
        }

        return $raw;
    }

    // ============================================================
    // KUZ – Szerbia
    // ============================================================

    private static function isKuz(string $raw): bool
    {
        return (bool) preg_match('/^KUZ[0-9\/]+$/', $raw);
    }

    private static function normalizeKuz(string $raw): string
    {
        return $raw;
    }
}