<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\RuleSuggestion;
use App\Policies\RuleSuggestionPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        RuleSuggestion::class => RuleSuggestionPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}