<?php

namespace App\Services\Dog;

use App\Models\Dog;
use App\Models\DogEvent;
use App\Models\DogOwnership;
use App\Models\DogChampionship;
use App\Models\DogHealthRecord;

class DogTimelineService
{
    public function getTimeline(int $dogId): array
    {
        $dog = Dog::findOrFail($dogId);

        return [
            'dog'          => $dog,
            'events'       => DogEvent::where('dog_id', $dogId)->orderBy('date')->get(),
            'ownerships'   => DogOwnership::where('dog_id', $dogId)->orderBy('from_date')->get(),
            'championships'=> DogChampionship::where('dog_id', $dogId)->orderBy('date')->get(),
            'health'       => DogHealthRecord::where('dog_id', $dogId)->orderBy('date')->get(),
        ];
    }
}
