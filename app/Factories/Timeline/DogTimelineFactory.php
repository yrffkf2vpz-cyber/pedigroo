<?php

namespace App\Factories\Timeline;

use App\Services\Timeline\DogTimelineService;

class DogTimelineFactory
{
    public function __construct(
        protected DogTimelineService $service
    ) {}

    public function birth(int $dogId, array $data = [])
    {
        return $this->service->addEvent($dogId, 'birth', $data);
    }

    public function ownershipChanged(int $dogId, int $oldOwner, int $newOwner)
    {
        return $this->service->addEvent($dogId, 'ownership_changed', [
            'old_owner' => $oldOwner,
            'new_owner' => $newOwner,
        ]);
    }

    public function healthTest(int $dogId, string $test, string $result)
    {
        return $this->service->addEvent($dogId, 'health_test', [
            'test' => $test,
            'result' => $result,
        ]);
    }

    public function showResult(int $dogId, array $result)
    {
        return $this->service->addEvent($dogId, 'show_result', $result);
    }

    public function death(int $dogId, array $data = [])
    {
        return $this->service->addEvent($dogId, 'death', $data);
    }
}