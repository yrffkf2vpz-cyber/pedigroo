<?php

namespace App\Factories\Timeline;

use App\Services\Timeline\BreederTimelineService;

class BreederTimelineFactory
{
    public function __construct(
        protected BreederTimelineService $service
    ) {}

    public function started(int $breederId)
    {
        return $this->service->addEvent($breederId, 'breeder_started');
    }

    public function kennelJoined(int $breederId, int $kennelId)
    {
        return $this->service->addEvent($breederId, 'kennel_joined', [
            'kennel_id' => $kennelId,
        ]);
    }

    public function kennelLeft(int $breederId, int $kennelId)
    {
        return $this->service->addEvent($breederId, 'kennel_left', [
            'kennel_id' => $kennelId,
        ]);
    }

    public function breedStarted(int $breederId, int $breedId)
    {
        return $this->service->addEvent($breederId, 'breed_started', [
            'breed_id' => $breedId,
        ]);
    }

    public function breedStopped(int $breederId, int $breedId)
    {
        return $this->service->addEvent($breederId, 'breed_stopped', [
            'breed_id' => $breedId,
        ]);
    }
}