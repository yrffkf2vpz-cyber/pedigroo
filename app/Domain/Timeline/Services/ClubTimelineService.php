<?php

namespace App\Services\Timeline;

use App\Models\Timeline\ClubTimeline;

class ClubTimelineService extends TimelineServiceBase
{
    protected function model(): ClubTimeline
    {
        return new ClubTimeline();
    }
}