<?php

namespace App\Services\Timeline;

use App\Models\Timeline\BreedTimeline;

class BreedTimelineService extends TimelineServiceBase
{
    protected function model(): BreedTimeline
    {
        return new BreedTimeline();
    }
}