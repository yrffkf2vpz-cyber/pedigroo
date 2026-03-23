<?php

namespace App\Services\Dog;

use Illuminate\Support\Facades\DB;

class DogBehaviourService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_behaviour_records')
            ->where('dog_id', $dogId)
            ->orderBy('observed_at', 'desc')
            ->get()
            ->toArray();
    }
}
