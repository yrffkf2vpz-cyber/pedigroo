<?php

namespace App\Services\Activation;

use Illuminate\Support\Facades\DB;

class DogActivationService
{
    /**
     * A NormalizePipelineService által előállított normalizált rekordot
     * véglegesíti a pd_dogs táblában.
     */
    public function activate(array $normalized): int
    {
        $dog = $normalized['dog'];

        // A végleges mezők összeállítása
        $final = [
            'name'        => $dog['real_name'],
            'prefix'      => $dog['real_prefix'],
            'lastname'    => $dog['real_lastname'],
            'firstname'   => $dog['real_firstname'],

            'reg_no'      => $dog['real_reg_no'],
            'reg_prefix'  => $dog['reg_prefix'],
            'reg_number'  => $dog['reg_number'],
            'reg_year'    => $dog['reg_year'],
            'reg_country' => $dog['reg_country'],
            'reg_issuer'  => $dog['reg_issuer'],

            'color'       => $dog['real_color'],
            'country'     => $dog['real_country'],
            'breed_id'    => $dog['breed_id'],

            // ⭐ SZÜLŐK
            'father_id'   => $dog['father_id'] ?? null,
            'mother_id'   => $dog['mother_id'] ?? null,

            'ai_used'     => $dog['ai_used'],
            'confidence'  => $dog['confidence'],
        ];

        // Megpróbáljuk megtalálni a végleges kutyát reg_no alapján
        $existing = DB::table('pd_dogs')
            ->where('reg_no', $dog['real_reg_no'])
            ->first();

        if ($existing) {
            // FRISSÍTÉS
            DB::table('pd_dogs')
                ->where('id', $existing->id)
                ->update($final);

            $dogId = $existing->id;
        } else {
            // LÉTREHOZÁS
            $dogId = DB::table('pd_dogs')->insertGetId($final);
        }

        // További modulok (health, results, titles, points)
        $this->storeHealth($dogId, $normalized['health']);
        $this->storeResults($dogId, $normalized['results']);
        $this->storeTitles($dogId, $normalized['titles']);
        $this->storePoints($dogId, $normalized['points']);

        return $dogId;
    }

    private function storeHealth(int $dogId, array $health): void
    {
        DB::table('pd_health')->updateOrInsert(
            ['dog_id' => $dogId],
            $health
        );
    }

    private function storeResults(int $dogId, array $results): void
    {
        foreach ($results as $result) {
            DB::table('pd_results')->insert([
                'dog_id' => $dogId,
                'event'  => $result['event'] ?? null,
                'judge'  => $result['judge'] ?? null,
                'class'  => $result['class'] ?? null,
                'grade'  => $result['grade'] ?? null,
                'title'  => $result['title'] ?? null,
            ]);
        }
    }

    private function storeTitles(int $dogId, array $titles): void
    {
        foreach ($titles as $title) {
            DB::table('pd_titles')->insert([
                'dog_id' => $dogId,
                'title'  => $title,
            ]);
        }
    }

    private function storePoints(int $dogId, array $points): void
    {
        DB::table('pd_points')->updateOrInsert(
            ['dog_id' => $dogId],
            [
                'total'     => $points['total'] ?? 0,
                'breakdown' => json_encode($points['breakdown'] ?? []),
            ]
        );
    }
}