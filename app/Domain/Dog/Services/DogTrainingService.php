<?php

namespace App\Services\Dog;

use Illuminate\Support\Facades\DB;

class DogTrainingService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_training_records')
            ->where('dog_id', $dogId)
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }
}
