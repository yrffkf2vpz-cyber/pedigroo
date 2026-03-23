<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureDashboardAccess
{
    /**
     * Engedélyezett szerepkörök
     */
    protected array $allowedRoles = [
        'superadmin',
        'developer',
        'admin',        // opcionális, ha később bővül a dashboard
    ];

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user) {
            Log::warning('Dashboard access denied: not logged in');
            abort(403, 'Nincs bejelentkezve.');
        }

        if (!in_array($user->role, $this->allowedRoles, true)) {
            Log::warning('Dashboard access denied: insufficient role', [
                'user_id' => $user->id,
                'role'    => $user->role,
            ]);

            abort(403, 'Nincs jogosultságod a dashboardhoz.');
        }

        Log::info('Dashboard access granted', [
            'user_id' => $user->id,
            'role'    => $user->role,
        ]);

        return $next($request);
    }
}
