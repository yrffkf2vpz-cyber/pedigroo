<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class KennelPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) BREEDER PD OLDALON (name + country)
            $breederId = null;

            if ($sandbox->breeder_name) {
                $breederId = DB::table('pd_breeders')
                    ->where('name', $sandbox->breeder_name)
                    ->where('country', $sandbox->country)
                    ->value('id');

                if (!$breederId) {
                    $breederId = DB::table('pd_breeders')->insertGetId([
                        'name'       => $sandbox->breeder_name,
                        'country'    => $sandbox->country,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 2) OWNER PD OLDALON (name + country)
            $ownerId = null;

            if ($sandbox->owner_name) {
                $ownerId = DB::table('pd_owners')
                    ->where('name', $sandbox->owner_name)
                    ->where('country', $sandbox->country)
                    ->value('id');

                if (!$ownerId) {
                    $ownerId = DB::table('pd_owners')->insertGetId([
                        'name'       => $sandbox->owner_name,
                        'country'    => $sandbox->country,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 3) DUPLIKÁCIÓ ELLENŐRZÉS KENNELRE
            $existing = DB::table('pd_kennels')
                ->where('name', $sandbox->name)
                ->where('country', $sandbox->country)
                ->where('breeder_id', $breederId)
                ->where('owner_id', $ownerId)
                ->value('id');

            if ($existing) {

                DB::table('pd_kennels')
                    ->where('id', $existing)
                    ->update([
                        'updated_at' => now(),
                    ]);

                $finalId = $existing;

            } else {

                $finalId = DB::table('pd_kennels')->insertGetId([
                    'name'       => $sandbox->name,
                    'country'    => $sandbox->country,
                    'breeder_id' => $breederId,
                    'owner_id'   => $ownerId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 4) SANDBOX AUDIT UPDATE
            DB::table('pedroo_kennels')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => "Promoted to pd_kennels (ID: {$finalId})",
                ]);

            return $finalId;
        });
    }
}