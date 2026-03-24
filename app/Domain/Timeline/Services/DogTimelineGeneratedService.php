<?php

namespace App\Services\Timeline;

use App\Models\Timeline\DogTimelineGenerated;

class DogTimelineGeneratedService extends TimelineServiceBase
{
    protected function model(): DogTimelineGenerated
    {
        return new DogTimelineGenerated();
    }
}