<?php

namespace App\Factories\Timeline;

use App\Services\Timeline\KennelTimelineService;

class KennelTimelineFactory
{
    public function __construct(
        protected KennelTimelineService $service
    ) {}

    public function founded(int $kennelId, array $data = [])
    {
        return $this->service->addEvent($kennelId, 'kennel_founded', $data);
    }

    public function newDog(int $kennelId, int $dogId)
    {
        return $this->service->addEvent($kennelId, 'new_dog_acquired', [
            'dog_id' => $dogId,
        ]);
    }

    public function champion(int $kennelId, int $dogId, string $title)
    {
        return $this->service->addEvent($kennelId, 'champion_title', [
            'dog_id' => $dogId,
            'title' => $title,
        ]);
    }

    public function breederJoined(int $kennelId, int $breederId)
    {
        return $this->service->addEvent($kennelId, 'breeder_joined', [
            'breeder_id' => $breederId,
        ]);
    }

    public function breederLeft(int $kennelId, int $breederId)
    {
        return $this->service->addEvent($kennelId, 'breeder_left', [
            'breeder_id' => $breederId,
        ]);
    }
}