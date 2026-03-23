<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AIAliasGeneratorService
{
    public function createAlias(string $domain, string $raw, string $canonical): void
    {
        $cleanRaw = trim(mb_strtolower($raw));
        $cleanCanonical = trim(mb_strtolower($canonical));

        // Ha már létezik, nem csinál semmit
        $exists = DB::table('pd_learning_aliases')
            ->where('domain', $domain)
            ->where('alias', $cleanRaw)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('pd_learning_aliases')->insert([
            'domain'    => $domain,
            'alias'     => $cleanRaw,
            'canonical' => $cleanCanonical,
        ]);
    }
}