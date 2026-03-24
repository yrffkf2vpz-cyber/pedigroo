<?php

namespace App\Services\Dog;

use Illuminate\Support\Facades\DB;

class DogEventParticipationService
{
    public function getForDog(int $dogId): array
    {
        return DB::table('pd_event_entries')
            ->where('dog_id', $dogId)
            ->orderBy('event_date', 'desc')
            ->get()
            ->toArray();
    }
}
