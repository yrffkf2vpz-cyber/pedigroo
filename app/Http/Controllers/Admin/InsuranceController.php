<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Admin\InsuranceService;

class InsuranceController extends Controller
{
    public function __construct(
        protected InsuranceService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $policies = $this->service->all();

            Log::info('Insurance list viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.insurance.index', [
                'policies' => $policies,
            ]);
        } catch (\Throwable $e) {
            Log::error('Insurance list load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.insurance.index', [
                'policies' => [],
                'error'    => 'A biztosítási adatok betöltése sikertelen.',
            ]);
        }
    }
}
