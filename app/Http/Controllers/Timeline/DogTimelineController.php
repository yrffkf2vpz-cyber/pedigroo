<?php

namespace App\Http\Controllers\Timeline;

use App\Models\Timeline\DogTimeline;
use App\Services\Timeline\DogTimelineService;
use App\Factories\Timeline\DogTimelineFactory;

class DogTimelineController extends BaseTimelineController
{
    protected function service()
    {
        return app(DogTimelineService::class);
    }

    protected function factory()
    {
        return app(DogTimelineFactory::class);
    }

    protected function model()
    {
        return new DogTimeline();
    }
}