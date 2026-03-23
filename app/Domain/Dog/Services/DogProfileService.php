<?php

namespace App\Services\Dog;

use Illuminate\Support\Facades\DB;

class DogProfileService
{
    protected DogChampionshipService $championships;

    public function __construct(DogChampionshipService $championships)
    {
        $this->championships = $championships;
    }

    public function getProfile(int $dogId): array
    {
        // 1) Alapadatok
        $dog = DB::table('pd_dogs')->where('id', $dogId)->first();
        if (!$dog) {
            throw new \Exception("Dog not found");
        }

        // 2) Képek
        $images = DB::table('pd_dog_images')
            ->where('dog_id', $dogId)
            ->orderBy('sort_order')
            ->get()
            ->toArray();

        // 3) Szülők
        $parents = DB::table('pd_dog_parents')
            ->where('dog_id', $dogId)
            ->get()
            ->toArray();

        // 4) Almok
        $litters = DB::table('pd_litters')
            ->where('sire_id', $dogId)
            ->orWhere('dam_id', $dogId)
            ->orderBy('born_at', 'desc')
            ->get()
            ->toArray();

        // 5) Egészségügyi adatok
        $health = DB::table('pd_health_records')
            ->where('dog_id', $dogId)
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();

        // 6) Eredmények (CAC, CACIB, BOB, stb.)
        $results = DB::table('pd_results')
            ->where('dog_id', $dogId)
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();

        // 7) Championship címek
        $championships = $this->championships->getForDog($dogId);

        // 8) Tulajdonos / kennel
        $owner = DB::table('pd_owners')
            ->where('id', $dog->owner_id ?? 0)
            ->first();

        $kennel = DB::table('pd_kennels')
            ->where('id', $dog->kennel_id ?? 0)
            ->first();

        // 9) Statisztikák
        $stats = [
            'championship_count' => count($championships),
            'result_count'       => count($results),
            'litter_count'       => count($litters),
        ];

        return [
            'dog'           => $dog,
            'images'        => $images,
            'parents'       => $parents,
            'litters'       => $litters,
            'health'        => $health,
            'results'       => $results,
            'championships' => $championships,
            'owner'         => $owner,
            'kennel'        => $kennel,
            'stats'         => $stats,
        ];
    }
}