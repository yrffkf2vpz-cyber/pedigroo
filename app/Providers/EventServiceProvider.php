<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\RecordNeedsReview;
use App\Listeners\SendReviewAlert;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        RecordNeedsReview::class => [
            SendReviewAlert::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
