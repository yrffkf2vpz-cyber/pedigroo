<?php

namespace App\Services\Vet;

use Illuminate\Support\Facades\DB;

class VetVisitService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_vet_visits')
            ->where('dog_id', $dogId)
            ->orderBy('visit_date', 'desc')
            ->get()
            ->toArray();
    }
}
