<?php

namespace App\Http\Controllers\Kennel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Kennel\KennelBoardingService;

class KennelBoardingController extends Controller
{
    public function __construct(
        protected KennelBoardingService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $kennelId, Request $request)
    {
        try {
            $bookings = $this->service->getForKennel($kennelId);

            Log::info('Kennel boarding viewed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
            ]);

            return view('admin.kennel.boarding', [
                'bookings' => $bookings,
                'kennelId' => $kennelId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Kennel boarding load failed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
                'error'     => $e->getMessage(),
            ]);

            return view('admin.kennel.boarding', [
                'bookings' => [],
                'kennelId' => $kennelId,
                'error'    => 'A panzió foglalások betöltése sikertelen.',
            ]);
        }
    }
}
