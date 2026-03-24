<?php

namespace App\Services\Timeline;

use App\Models\Timeline\DogTimeline;

class DogTimelineService extends TimelineServiceBase
{
    protected function model(): DogTimeline
    {
        return new DogTimeline();
    }
}