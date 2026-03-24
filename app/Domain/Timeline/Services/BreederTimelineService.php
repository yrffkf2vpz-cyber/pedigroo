<?php

namespace App\Services\Timeline;

use App\Models\Timeline\BreederTimeline;

class BreederTimelineService extends TimelineServiceBase
{
    protected function model(): BreederTimeline
    {
        return new BreederTimeline();
    }
}