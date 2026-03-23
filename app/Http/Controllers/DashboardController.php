<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dashboard\DashboardService;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $service
    ) {
        // admin jogosultság szükséges
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $stats = $this->service->getStats();

            Log::info('Dashboard viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.dashboard.index', [
                'stats' => $stats,
            ]);

        } catch (\Throwable $e) {

            Log::error('Dashboard load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dashboard.index', [
                'stats' => [],
                'error' => 'A dashboard betöltése sikertelen.',
            ]);
        }
    }
}
