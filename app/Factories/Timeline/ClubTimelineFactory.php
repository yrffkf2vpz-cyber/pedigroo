<?php

namespace App\Factories\Timeline;

use App\Services\Timeline\ClubTimelineService;

class ClubTimelineFactory
{
    public function __construct(
        protected ClubTimelineService $service
    ) {}

    public function memberJoined(int $clubId, int $userId)
    {
        return $this->service->addEvent($clubId, 'member_joined', [
            'user_id' => $userId,
        ]);
    }

    public function memberLeft(int $clubId, int $userId)
    {
        return $this->service->addEvent($clubId, 'member_left', [
            'user_id' => $userId,
        ]);
    }

    public function showEvent(int $clubId, array $event)
    {
        return $this->service->addEvent($clubId, 'show_event', $event);
    }

    public function ruleChanged(int $clubId, array $changes)
    {
        return $this->service->addEvent($clubId, 'rule_change', $changes);
    }
}