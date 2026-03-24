<?php

namespace App\Services\Dog;

use Illuminate\Support\Facades\DB;

class DogBoardingService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_boarding')
            ->where('dog_id', $dogId)
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();
    }
}
