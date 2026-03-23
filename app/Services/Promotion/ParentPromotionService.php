<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;

class ParentPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) CHILD DOG ID (child_name → pd_dogs.id)
            $childId = DB::table('pd_dogs')
                ->where('name', $sandbox->child_name)
                ->value('id');

            if (!$childId) {
                throw new \Exception("Child dog not found in PD system: {$sandbox->child_name}");
            }

            // 2) PARENT DOG ID (parent_name → pd_dogs.id)
            $parentId = DB::table('pd_dogs')
                ->where('name', $sandbox->parent_name)
                ->value('id');

            if (!$parentId) {
                throw new \Exception("Parent dog not found in PD system: {$sandbox->parent_name}");
            }

            // 3) DUPLIKÁCIÓ ELLENŐRZÉS
            $existing = DB::table('pd_parents')
                ->where('dog_id', $childId)
                ->where('parent_id', $parentId)
                ->where('relation', $sandbox->relation)
                ->value('id');

            if ($existing) {

                DB::table('pd_parents')
                    ->where('id', $existing)
                    ->update([
                        'updated_at' => now(),
                    ]);

                $finalId = $existing;

            } else {

                // 4) INSERT PD OLDALRA
                $finalId = DB::table('pd_parents')->insertGetId([
                    'dog_id'     => $childId,
                    'parent_id'  => $parentId,
                    'relation'   => $sandbox->relation,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 5) SANDBOX AUDIT UPDATE
            DB::table('pedroo_parents')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => "Promoted to pd_parents (ID: {$finalId})",
                ]);

            return $finalId;
        });
    }
}