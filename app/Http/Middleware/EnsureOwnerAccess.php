<?php

namespace App\Http\Middleware;

use Closure;

class EnsureOwnerAccess
{
    public function handle($request, Closure $next)
    {
        $dogId = (int) $request->route('dogId');

        if (!auth()->user()->ownsDog($dogId)) {
            abort(403);
        }

        return $next($request);
    }
}
