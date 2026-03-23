<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Admin\ComplianceService;

class ComplianceController extends Controller
{
    public function __construct(
        protected ComplianceService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $items = $this->service->all();

            Log::info('Compliance dashboard viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.compliance.index', [
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            Log::error('Compliance dashboard load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.compliance.index', [
                'items' => [],
                'error' => 'A megfelelőségi adatok betöltése sikertelen.',
            ]);
        }
    }
}
