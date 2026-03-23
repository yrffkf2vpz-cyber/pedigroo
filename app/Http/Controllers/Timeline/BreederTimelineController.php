<?php

namespace App\Http\Controllers\Timeline;

use App\Models\Timeline\BreederTimeline;
use App\Services\Timeline\BreederTimelineService;
use App\Factories\Timeline\BreederTimelineFactory;

class BreederTimelineController extends BaseTimelineController
{
    protected function service()
    {
        return app(BreederTimelineService::class);
    }

    protected function factory()
    {
        return app(BreederTimelineFactory::class);
    }

    protected function model()
    {
        return new BreederTimeline();
    }
}