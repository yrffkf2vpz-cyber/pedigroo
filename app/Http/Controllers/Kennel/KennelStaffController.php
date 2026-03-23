<?php

namespace App\Http\Controllers\Kennel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Kennel\KennelStaffService;

class KennelStaffController extends Controller
{
    public function __construct(
        protected KennelStaffService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $kennelId, Request $request)
    {
        try {
            $staff = $this->service->getForKennel($kennelId);

            Log::info('Kennel staff viewed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
            ]);

            return view('admin.kennel.staff', [
                'staff'    => $staff,
                'kennelId' => $kennelId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Kennel staff load failed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
                'error'     => $e->getMessage(),
            ]);

            return view('admin.kennel.staff', [
                'staff'    => [],
                'kennelId' => $kennelId,
                'error'    => 'A kennel személyzeti adatok betöltése sikertelen.',
            ]);
        }
    }
}
