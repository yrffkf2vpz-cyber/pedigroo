<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class DogResultService
{
    /**
     * Kutya show eredmény feldolgozás (privát Pedroo réteg)
     *
     * @param object $parsed
     * Kötelező:
     *   - dog_name
     *   - show_id
     *   - show_result_id
     *
     * @return int dog_event_result_id
     */
    public function processDogShowResult(object $parsed): int
    {
        $dogName      = trim($parsed->dog_name ?? '');
        $showId       = $parsed->show_id ?? null;
        $showResultId = $parsed->show_result_id ?? null;

        if (!$dogName || !$showId || !$showResultId) {
            throw new \InvalidArgumentException('Hiányos dog show result adatok.');
        }

        // HASH a duplikáció ellen
        $hash = hash('sha256', strtolower(
            $dogName . '|' . $showId . '|' . $showResultId
        ));

        // Keresés
        $existing = DB::table('dog_event_results')
            ->where('hash', $hash)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // Beszúrás
        return DB::table('dog_event_results')->insertGetId([
            'dog_name'       => $dogName,
            'show_id'        => $showId,
            'show_result_id' => $showResultId,
            'hash'           => $hash,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    /**
     * Sport eredmény feldolgozás
     */
    public function processDogSportResult(object $parsed): int
    {
        $dogName   = trim($parsed->dog_name ?? '');
        $eventId   = $parsed->event_id ?? null;
        $result    = $parsed->result ?? null;

        if (!$dogName || !$eventId || !$result) {
            throw new \InvalidArgumentException('Hiányos dog sport result adatok.');
        }

        $hash = hash('sha256', strtolower(
            $dogName . '|' . $eventId . '|' . $result
        ));

        $existing = DB::table('dog_sport_results')
            ->where('hash', $hash)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        return DB::table('dog_sport_results')->insertGetId([
            'dog_name'   => $dogName,
            'event_id'   => $eventId,
            'result'     => $result,
            'hash'       => $hash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Working eredmény feldolgozás
     */
    public function processDogWorkingResult(object $parsed): int
    {
        $dogName = trim($parsed->dog_name ?? '');
        $eventId = $parsed->event_id ?? null;
        $score   = $parsed->score ?? null;

        if (!$dogName || !$eventId || !$score) {
            throw new \InvalidArgumentException('Hiányos dog working result adatok.');
        }

        $hash = hash('sha256', strtolower(
            $dogName . '|' . $eventId . '|' . $score
        ));

        $existing = DB::table('dog_working_results')
            ->where('hash', $hash)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        return DB::table('dog_working_results')->insertGetId([
            'dog_name'   => $dogName,
            'event_id'   => $eventId,
            'score'      => $score,
            'hash'       => $hash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Behavior teszt eredmény feldolgozás
     */
    public function processDogBehaviorResult(object $parsed): int
    {
        $dogName = trim($parsed->dog_name ?? '');
        $testId  = $parsed->test_type_id ?? null;
        $result  = $parsed->result ?? null;

        if (!$dogName || !$testId || !$result) {
            throw new \InvalidArgumentException('Hiányos dog behavior result adatok.');
        }

        $hash = hash('sha256', strtolower(
            $dogName . '|' . $testId . '|' . $result
        ));

        $existing = DB::table('dog_behavior_results')
            ->where('hash', $hash)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        return DB::table('dog_behavior_results')->insertGetId([
            'dog_name'   => $dogName,
            'test_type_id' => $testId,
            'result'     => $result,
            'hash'       => $hash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Health rekord feldolgozás
     */
    public function processHealthRecord(object $parsed): int
    {
        $dogName = trim($parsed->dog_name ?? '');
        $typeId  = $parsed->record_type_id ?? null;
        $codeId  = $parsed->result_code_id ?? null;

        if (!$dogName || !$typeId || !$codeId) {
            throw new \InvalidArgumentException('Hiányos health record adatok.');
        }

        $hash = hash('sha256', strtolower(
            $dogName . '|' . $typeId . '|' . $codeId
        ));

        $existing = DB::table('health_records')
            ->where('hash', $hash)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        return DB::table('health_records')->insertGetId([
            'dog_name'       => $dogName,
            'record_type_id' => $typeId,
            'result_code_id' => $codeId,
            'hash'           => $hash,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }
}