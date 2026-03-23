<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class BreederPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) DUPLIKÁCIÓ ELLENŐRZÉS PD OLDALON
            $existing = DB::table('pd_breeders')
                ->where('name', $sandbox->name)
                ->where('country', $sandbox->country)
                ->value('id');

            if ($existing) {

                // UPDATE (ha kell audit vagy frissítés)
                DB::table('pd_breeders')
                    ->where('id', $existing)
                    ->update([
                        'updated_at' => now(),
                    ]);

                $finalId = $existing;

            } else {

                // 2) INSERT PD OLDALRA
                $finalId = DB::table('pd_breeders')->insertGetId([
                    'name'       => $sandbox->name,
                    'country'    => $sandbox->country,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3) SANDBOX AUDIT UPDATE
            DB::table('pedroo_breeders')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => $sandbox->notes ?? null,
                ]);

            return $finalId;
        });
    }
}