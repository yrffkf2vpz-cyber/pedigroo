<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class HistoryPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) DUPLIKÁCIÓ ELLENŐRZÉS PD HISTORY OLDALON
            $existing = DB::table('pd_history')
                ->where('entity_id', $sandbox->entity_id)
                ->where('type', $sandbox->type)
                ->value('id');

            if ($existing) {

                // UPDATE (audit frissítés)
                DB::table('pd_history')
                    ->where('id', $existing)
                    ->update([
                        'updated_at' => now(),
                        'notes'      => $sandbox->notes ?? null,
                    ]);

                $finalId = $existing;

            } else {

                // 2) INSERT PD HISTORY OLDALRA
                $finalId = DB::table('pd_history')->insertGetId([
                    'entity_id'  => $sandbox->entity_id,
                    'type'       => $sandbox->type,
                    'data'       => json_encode($sandbox->data ?? []),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'notes'      => $sandbox->notes ?? null,
                ]);
            }

            // 3) SANDBOX AUDIT UPDATE
            DB::table('pedroo_history_sandbox')
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