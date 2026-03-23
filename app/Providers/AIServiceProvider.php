<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\DogImported;
use App\Events\DogCreatedByUser;
use App\Events\DogUpdatedByAdmin;

use App\Services\AI\UnknownDetectorService;

class AIServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(DogImported::class, function (DogImported $event) {
            app(UnknownDetectorService::class)->detect($event->rawDog);
        });

        Event::listen(DogCreatedByUser::class, function (DogCreatedByUser $event) {
            app(UnknownDetectorService::class)->detect($event->rawDog);
        });

        Event::listen(DogUpdatedByAdmin::class, function (DogUpdatedByAdmin $event) {
            app(UnknownDetectorService::class)->detect($event->rawDog);
        });
    }
}