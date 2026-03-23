<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class FuzzyMatchService
{
    public function suggest(string $domain, string $raw): ?string
    {
        $clean = trim(mb_strtolower($raw));

        $aliases = DB::table('pd_learning_aliases')
            ->where('domain', $domain)
            ->get();

        if ($aliases->isEmpty()) {
            return null;
        }

        $best = null;
        $bestScore = 999;

        foreach ($aliases as $row) {
            $distance = levenshtein($clean, mb_strtolower($row->alias));

            if ($distance < $bestScore) {
                $bestScore = $distance;
                $best = $row->canonical;
            }
        }

        if ($bestScore <= 3) {
            return $best;
        }

        return null;
    }
}