<?php

namespace App\Http\Controllers\Kennel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Kennel\KennelComplianceService;

class KennelComplianceController extends Controller
{
    public function __construct(
        protected KennelComplianceService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $kennelId, Request $request)
    {
        try {
            $compliance = $this->service->getForKennel($kennelId);

            Log::info('Kennel compliance viewed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
            ]);

            return view('admin.kennel.compliance', [
                'compliance' => $compliance,
                'kennelId'   => $kennelId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Kennel compliance load failed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
                'error'     => $e->getMessage(),
            ]);

            return view('admin.kennel.compliance', [
                'compliance' => [],
                'kennelId'   => $kennelId,
                'error'      => 'A kennel megfelelőségi adatok betöltése sikertelen.',
            ]);
        }
    }
}
