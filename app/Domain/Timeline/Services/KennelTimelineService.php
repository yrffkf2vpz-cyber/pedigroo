<?php

namespace App\Services\Timeline;

use App\Models\Timeline\KennelTimeline;

class KennelTimelineService extends TimelineServiceBase
{
    protected function model(): KennelTimeline
    {
        return new KennelTimeline();
    }
}