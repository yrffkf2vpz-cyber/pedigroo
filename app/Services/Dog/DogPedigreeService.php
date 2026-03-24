<?php

namespace App\Services\Dog;

use Illuminate\Support\Facades\DB;

class DogPedigreeService
{
    public function getPedigree(int $dogId): array
    {
        return DB::table('pd_pedigree')
            ->where('dog_id', $dogId)
            ->orderBy('generation')
            ->get()
            ->toArray();
    }
}
