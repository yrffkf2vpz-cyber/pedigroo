<?php

namespace App\Services\Vet;

use Illuminate\Support\Facades\DB;

class VaccinationService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_vaccinations')
            ->where('dog_id', $dogId)
            ->orderBy('date', 'desc')
            ->get()
            ->toArray();
    }
}
