<?php

namespace App\Services\Dog;

use Illuminate\Support\Facades\DB;

class DogChampionshipService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_championships as c')
            ->join('title_definitions as t', 't.id', '=', 'c.title_definition_id')
            ->leftJoin('pd_events as e', 'e.id', '=', 'c.event_id')
            ->leftJoin('countries as co', 'co.id', '=', 'c.country_id')
            ->where('c.dog_id', $dogId)
            ->orderBy('c.date', 'desc')
            ->select([
                'c.id',
                'c.date',
                'c.source',
                'c.external_id',

                't.title_code',
                't.title_name',

                'e.name as event_name',
                'co.code as country_code',
            ])
            ->get()
            ->toArray();
    }
}