<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class HealthPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) DOG ID PROMOTE (legacy → pd_dogs.id)
            $dogId = DB::table('pd_dogs')
                ->where('legacy_id', $sandbox->dog_id)
                ->value('id');

            if (!$dogId) {
                throw new \Exception("Dog not found in PD system for legacy ID {$sandbox->dog_id}");
            }

            // 2) RECORD TYPE ID (HD / ED / DM)
            $recordTypeId = DB::table('pd_health_record_types')
                ->where('name', $sandbox->type)
                ->value('id');

            if (!$recordTypeId) {
                throw new \Exception("Unknown health record type: {$sandbox->type}");
            }

            // 3) RESULT CODE ID (A, B, 0/0, CLEAR, stb.)
            $resultCodeId = DB::table('pd_health_result_codes')
                ->where('code', $sandbox->value)
                ->value('id');

            if (!$resultCodeId) {
                throw new \Exception("Unknown health result code: {$sandbox->value}");
            }

            // 4) DUPLIKÁCIÓ ELLENŐRZÉS
            $existing = DB::table('pd_health_records')
                ->where('dog_id', $dogId)
                ->where('record_type_id', $recordTypeId)
                ->where('result_code_id', $resultCodeId)
                ->value('id');

            if ($existing) {

                DB::table('pd_health_records')
                    ->where('id', $existing)
                    ->update([
                        'updated_at' => now(),
                    ]);

                $finalId = $existing;

            } else {

                $finalId = DB::table('pd_health_records')->insertGetId([
                    'dog_id'          => $dogId,
                    'record_type_id'  => $recordTypeId,
                    'result_code_id'  => $resultCodeId,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }

            // 5) SANDBOX AUDIT UPDATE
            DB::table('pedroo_health_records')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => "Promoted to pd_health_records (ID: {$finalId})",
                ]);

            return $finalId;
        });
    }
}