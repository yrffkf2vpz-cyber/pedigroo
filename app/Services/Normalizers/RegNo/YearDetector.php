<?php

namespace App\Services\Normalizers\RegNo;

class YearDetector
{
    public function detect(string $raw): ?int
    {
        // .../99
        if (preg_match('/\/(\d{2})$/', trim($raw), $m)) {
            $yy = (int)$m[1];
            return $yy < 30 ? 2000 + $yy : 1900 + $yy;
        }

        return null;
    }
}