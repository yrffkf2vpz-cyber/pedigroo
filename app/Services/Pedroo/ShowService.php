<?php

namespace App\Services\Pedroo;

use Illuminate\Support\Facades\DB;

class ShowService
{
    /**
     * Show feldolgozás
     *
     * @param object $parsedShow
     * Kötelező mezők:
     *   - name
     *   - country_id
     *   - city
     *   - date
     *
     * Opcionális:
     *   - external_id
     *   - source
     *   - judges (tömb)
     *
     * @return int show_id
     */
    public function processShow(object $parsedShow): int
    {
        $name       = trim($parsedShow->name ?? '');
        $countryId  = $parsedShow->country_id ?? null;
        $city       = trim($parsedShow->city ?? '');
        $date       = $parsedShow->date ?? null;
        $externalId = $parsedShow->external_id ?? null;
        $source     = $parsedShow->source ?? 'pedroo';
        $judges     = $parsedShow->judges ?? [];

        if (!$name || !$countryId || !$city || !$date) {
            throw new \InvalidArgumentException('Hiányos show adatok (name, country_id, city, date kötelező).');
        }

        // 1) LOCATION FELDOLGOZÁS
        $locationId = $this->processLocation($countryId, $city);

        // 2) HASH A SHOWHOZ
        $hash = hash('sha256', strtolower(
            $name . '|' . $date . '|' . $city . '|' . $countryId . '|' . $source
        ));

        // 3) KERESÉS HASH ALAPJÁN
        $show = DB::table('show_shows')
            ->where('hash', $hash)
            ->first();

        // 4) KERESÉS external_id + source ALAPJÁN
        if (!$show && $externalId) {
            $show = DB::table('show_shows')
                ->where('external_id', $externalId)
                ->where('source', $source)
                ->first();
        }

        // 5) HA NINCS → BESZÚRÁS
        if (!$show) {
            $showId = DB::table('show_shows')->insertGetId([
                'name'        => $name,
                'location_id' => $locationId,
                'date'        => $date,
                'hash'        => $hash,
                'external_id' => $externalId,
                'source'      => $source,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } else {
            // 6) HA VAN → FRISSÍTÉS
            DB::table('show_shows')
                ->where('id', $show->id)
                ->update([
                    'name'       => strlen($name) > strlen($show->name) ? $name : $show->name,
                    'updated_at' => now(),
                ]);

            $showId = $show->id;
        }

        // 7) BÍRÓK FELDOLGOZÁSA
        foreach ($judges as $judgeNameRaw) {
            $judgeId = $this->processJudge($judgeNameRaw);

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

        return $showId;
    }

    /**
     * Helyszín feldolgozás
     */
    protected function processLocation(int $countryId, string $city): int
    {
        $location = DB::table('show_locations')
            ->where('country_id', $countryId)
            ->whereRaw('LOWER(city) = ?', [strtolower($city)])
            ->first();

        if ($location) {
            return $location->id;
        }

        return DB::table('show_locations')->insertGetId([
            'country_id' => $countryId,
            'city'       => $city,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Bíró normalizálás + beszúrás
     */
    protected function processJudge(string $rawName): int
    {
        if (!$rawName) {
            return null;
        }

        // Országkód kinyerése (pl. "John Smith (ROU)")
        preg_match('/\((.*?)\)$/', $rawName, $m);
        $countryCode = $m[1] ?? null;

        // Név megtisztítása
        $cleanName = trim(preg_replace('/\((.*?)\)$/', '', $rawName));

        // Bíró keresése
        $judge = DB::table('show_judges')
            ->whereRaw('LOWER(name) = ?', [strtolower($cleanName)])
            ->first();

        if ($judge) {
            return $judge->id;
        }

        // Ország ID lekérése
        $countryId = null;
        if ($countryCode) {
            $countryId = DB::table('countries')
                ->where('iso_code', $countryCode)
                ->value('id');
        }

        // Bíró létrehozása
        return DB::table('show_judges')->insertGetId([
            'name'       => $cleanName,
            'country_id' => $countryId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}