<?php

namespace App\Http\Controllers\Timeline;

use App\Models\Timeline\ClubTimeline;
use App\Services\Timeline\ClubTimelineService;
use App\Factories\Timeline\ClubTimelineFactory;

class ClubTimelineController extends BaseTimelineController
{
    protected function service()
    {
        return app(ClubTimelineService::class);
    }

    protected function factory()
    {
        return app(ClubTimelineFactory::class);
    }

    protected function model()
    {
        return new ClubTimeline();
    }
}