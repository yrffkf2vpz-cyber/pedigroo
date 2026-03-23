<?php

namespace App\Services\Vet;

use Illuminate\Support\Facades\DB;

class MedicationService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_medications')
            ->where('dog_id', $dogId)
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }
}
