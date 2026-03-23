<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class ShowResultService
{
    /**
     * Show eredmény feldolgozás
     *
     * @param object $parsedResult
     * Kötelező mezők:
     *   - show_id
     *   - dog_name
     *   - class_type_id
     *   - qualification_id
     *
     * Opcionális:
     *   - placement
     *   - titles (tömb)
     *   - judge_name
     *   - ring
     *   - notes
     *
     * @return int show_result_id
     */
    public function processShowResult(object $parsedResult): int
    {
        $showId          = $parsedResult->show_id;
        $dogName         = trim($parsedResult->dog_name ?? '');
        $classTypeId     = $parsedResult->class_type_id ?? null;
        $qualificationId = $parsedResult->qualification_id ?? null;
        $placement       = $parsedResult->placement ?? null;
        $titles          = $parsedResult->titles ?? [];
        $judgeName       = $parsedResult->judge_name ?? null;
        $ring            = $parsedResult->ring ?? null;
        $notes           = $parsedResult->notes ?? null;

        if (!$showId || !$dogName || !$classTypeId || !$qualificationId) {
            throw new \InvalidArgumentException('Hiányos show result adatok.');
        }

        // 1) BÍRÓ FELDOLGOZÁSA (ha van)
        $judgeId = null;
        if ($judgeName) {
            $judgeId = $this->processJudge($judgeName);

            // kapcsolótábla biztosítása
            DB::table('show_show_judges')->updateOrInsert(
                [
                    'show_id'  => $showId,
                    'judge_id' => $judgeId,
                ],
                [
                    'updated_at' => now(),
                ]
            );
        }

        // 2) HASH A DUPLIKÁCIÓ ELLEN
        $hash = hash('sha256', strtolower(
            $showId . '|' . $dogName . '|' . $classTypeId . '|' . $qualificationId . '|' . ($placement ?? '')
        ));

        // 3) KERESÉS HASH ALAPJÁN
        $existing = DB::table('show_results')
            ->where('hash', $hash)
            ->first();

        if ($existing) {
            return $existing->id;
        }

        // 4) BESZÚRÁS
        $resultId = DB::table('show_results')->insertGetId([
            'show_id'          => $showId,
            'dog_name'         => $dogName,
            'class_type_id'    => $classTypeId,
            'qualification_id' => $qualificationId,
            'placement'        => $placement,
            'judge_id'         => $judgeId,
            'ring'             => $ring,
            'notes'            => $notes,
            'hash'             => $hash,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // 5) CÍMEK FELDOLGOZÁSA
        foreach ($titles as $titleId) {
            DB::table('show_titles')->insert([
                'show_result_id' => $resultId,
                'title_id'       => $titleId,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        return $resultId;
    }

    /**
     * Bíró normalizálás (ugyanaz, mint a ShowService-ben)
     */
    protected function processJudge(string $rawName): int
    {
        preg_match('/\((.*?)\)$/', $rawName, $m);
        $countryCode = $m[1] ?? null;

        $cleanName = trim(preg_replace('/\((.*?)\)$/', '', $rawName));

        $judge = DB::table('show_judges')
            ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
            ->first();

        if ($judge) {
            return $judge->id;
        }

        $countryId = null;
        if ($countryCode) {
            $countryId = DB::table('countries')
                ->where('iso_code', $countryCode)
                ->value('id');
        }

        return DB::table('show_judges')->insertGetId([
            'name'       => $cleanName,
            'country_id' => $countryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}