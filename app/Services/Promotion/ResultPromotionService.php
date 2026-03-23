<?php

namespace App\Services\Promotion;

use Illuminate\Support\Facades\DB;
use App\Services\Normalizers\EventPromotionNormalizer;
use App\Services\Normalizers\DogPromotionNormalizer;
use App\Services\Normalizers\QualificationPromotionNormalizer;
use App\Services\Normalizers\JudgePromotionNormalizer;
use App\Services\Normalizers\PlacementPromotionNormalizer;
use App\Services\Normalizers\ClassPromotionNormalizer;
use App\Services\Normalizers\RingPromotionNormalizer;

class ResultPromotionService
{
    public function promote(object $sandbox): int
    {
        return DB::transaction(function () use ($sandbox) {

            // 1) Promote related entities
            $eventId         = EventPromotionNormalizer::promote($sandbox->event_id);
            $dogId           = DogPromotionNormalizer::promoteFromName($sandbox->dog_name);
            $qualificationId = QualificationPromotionNormalizer::promote($sandbox->class_type);
            $judgeId         = JudgePromotionNormalizer::promote($sandbox->judge_name);
            $placementId     = PlacementPromotionNormalizer::promote($sandbox->placement);
            $classId         = ClassPromotionNormalizer::promote($sandbox->class_type);
            $ringId          = RingPromotionNormalizer::promote($sandbox->ring);

            // 2) Deduplication using NORMALIZED fields
           $existing = DB::table('pd_event_results')
    ->where('event_id', $eventId)
    ->where('dog_id', $dogId)
    ->where('class_id', $classId)
    ->where('placement_id', $placementId)
    ->where('ring_id', $ringId)
    ->value('id');

            if ($existing) {

                DB::table('pd_event_results')
                    ->where('id', $existing)
                    ->update([
                        'raw'          => $sandbox->raw,
                        'source'       => $sandbox->source,
                        'external_id'  => $sandbox->external_id,
                        'hash'         => $sandbox->hash,
                        'submitted_by' => $sandbox->submitted_by,
                        'updated_at'   => now(),
                    ]);

                $resultId = $existing;

            } else {

                $resultId = DB::table('pd_event_results')->insertGetId([
                    'event_id'         => $eventId,
                    'dog_id'           => $dogId,
                    'class_id'         => $classId,
                    'placement_id'     => $placementId,
                    'qualification_id' => $qualificationId,
                    'judge_id'         => $judgeId,
                    'ring_id'          => $ringId,
                    'source'           => $sandbox->source,
                    'external_id'      => $sandbox->external_id,
                    'hash'             => $sandbox->hash,
                    'raw'              => $sandbox->raw,
                    'submitted_by'     => $sandbox->submitted_by,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }

            // 4) Update sandbox audit
            DB::table('pedroo_results')
                ->where('id', $sandbox->id)
                ->update([
                    'status'     => 'promoted',
                    'updated_at' => now(),
                    'notes'      => 'Promoted to pd_event_results (ID: '.$resultId.')',
                ]);

            return $resultId;
        });
    }
}