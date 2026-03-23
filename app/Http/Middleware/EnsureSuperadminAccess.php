<?php

namespace App\Http\Middleware;

use Closure;

class EnsureSuperadminAccess
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->can('superadmin')) {
            abort(403);
        }

        return $next($request);
    }
}
