<?php

namespace App\Http\Controllers\Kennel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Kennel\KennelFacilityService;

class KennelFacilityController extends Controller
{
    public function __construct(
        protected KennelFacilityService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $kennelId, Request $request)
    {
        try {
            $facility = $this->service->get($kennelId);

            Log::info('Kennel facility viewed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
            ]);

            return view('admin.kennel.facility', [
                'facility' => $facility,
                'kennelId' => $kennelId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Kennel facility load failed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
                'error'     => $e->getMessage(),
            ]);

            return view('admin.kennel.facility', [
                'facility' => null,
                'kennelId' => $kennelId,
                'error'    => 'A kennel létesítmény adatainak betöltése sikertelen.',
            ]);
        }
    }
}
