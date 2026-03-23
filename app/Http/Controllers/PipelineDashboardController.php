<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Pipeline\PipelineDashboardService;

class PipelineDashboardController extends Controller
{
    public function __construct(
        protected PipelineDashboardService $service
    ) {
        // csak admin vagy superadmin érheti el
        $this->middleware(['auth', 'can:admin']);
    }

    /**
     * Pipeline taskok listája (utolsó 50)
     */
    public function index(Request $request)
    {
        try {
            $tasks = $this->service->getRecentTasks(50);

            Log::info('Pipeline dashboard viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.pipeline.dashboard', [
                'tasks' => $tasks,
            ]);

        } catch (\Throwable $e) {

            Log::error('Pipeline dashboard load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.pipeline.dashboard', [
                'tasks' => [],
                'error' => 'A pipeline dashboard betöltése sikertelen.',
            ]);
        }
    }
}
