<?php

namespace App\Http\Controllers\Timeline;

use App\Models\Timeline\KennelTimeline;
use App\Services\Timeline\KennelTimelineService;
use App\Factories\Timeline\KennelTimelineFactory;

class KennelTimelineController extends BaseTimelineController
{
    protected function service()
    {
        return app(KennelTimelineService::class);
    }

    protected function factory()
    {
        return app(KennelTimelineFactory::class);
    }

    protected function model()
    {
        return new KennelTimeline();
    }
}