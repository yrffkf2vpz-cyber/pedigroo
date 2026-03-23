<?php

namespace App\Http\Controllers\Timeline;

use App\Models\Timeline\BreedTimeline;
use App\Services\Timeline\BreedTimelineService;
use App\Factories\Timeline\BreedTimelineFactory;

class BreedTimelineController extends BaseTimelineController
{
    protected function service()
    {
        return app(BreedTimelineService::class);
    }

    protected function factory()
    {
        return app(BreedTimelineFactory::class);
    }

    protected function model()
    {
        return new BreedTimeline();
    }
}