<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Admin\BillingService;

class BillingController extends Controller
{
    public function __construct(
        protected BillingService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $invoices = $this->service->all();

            Log::info('Billing list viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.billing.index', [
                'invoices' => $invoices,
            ]);
        } catch (\Throwable $e) {
            Log::error('Billing list load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.billing.index', [
                'invoices' => [],
                'error'    => 'A számlázási adatok betöltése sikertelen.',
            ]);
        }
    }
}
