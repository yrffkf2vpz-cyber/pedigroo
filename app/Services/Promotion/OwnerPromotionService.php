<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class OwnerPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) DUPLIKÁCIÓ ELLENŐRZÉS PD OLDALON
            $existing = DB::table('pd_owners')
                ->where('name', $sandbox->name)
                ->where('country', $sandbox->country)
                ->value('id');

            if ($existing) {

                DB::table('pd_owners')
                    ->where('id', $existing)
                    ->update([
                        'updated_at' => now(),
                    ]);

                $finalId = $existing;

            } else {

                // 2) INSERT PD OLDALRA
                $finalId = DB::table('pd_owners')->insertGetId([
                    'name'       => $sandbox->name,
                    'country'    => $sandbox->country,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 3) SANDBOX AUDIT UPDATE
            DB::table('pedroo_owners')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => "Promoted to pd_owners (ID: {$finalId})",
                ]);

            return $finalId;
        });
    }
}