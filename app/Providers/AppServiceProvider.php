<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Promotion\PromotionRunner;
use App\Services\Promotion\ResultPromotionService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // ResultPromotionService singleton
        $this->app->singleton(ResultPromotionService::class);

        // PromotionRunner singleton
        $this->app->singleton(PromotionRunner::class);
    }

    public function boot(): void
    {
        // Runtime tracker integráció
        //$this->app->resolving(function ($object, $app) {
            //if (class_exists(\App\Modules\SystemScanner\FileUsageTracker::class)) {
                //try {
                    //$tracker = $app->make(\App\Modules\SystemScanner\FileUsageTracker::class);
                    //$tracker->hit((new \ReflectionClass($object))->getFileName());
                //} catch (\Throwable $e) {
                    // Ha valami nem injektálható, csendben továbbmegy
                //}
           // }
       // });
    }
}
