<?php

namespace App\Http\Middleware;

use Closure;

class EnsureAdminAccess
{
    public function handle($request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->can('admin')) {
            abort(403);
        }

        return $next($request);
    }
}
