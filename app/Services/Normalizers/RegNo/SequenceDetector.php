<?php

namespace App\Services\Normalizers\RegNo;

class SequenceDetector
{
    public function detect(string $raw): ?int
    {
        // MET.Ku.1955/H/23
        if (preg_match('/\.[A-Z][a-z]\.\s*(\d{1,6})[\/ ]?/', $raw, $m)) {
            return (int)$m[1];
        }

        // S 25326/10 → 25326
        if (preg_match('/\s(\d{1,7})[\/ ]?/', $raw, $m)) {
            return (int)$m[1];
        }

        return null;
    }
}