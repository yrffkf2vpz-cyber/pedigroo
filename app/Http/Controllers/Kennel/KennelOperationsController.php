<?php

namespace App\Http\Controllers\Kennel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Kennel\KennelOperationsService;

class KennelOperationsController extends Controller
{
    public function __construct(
        protected KennelOperationsService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $kennelId, Request $request)
    {
        try {
            $operations = $this->service->getForKennel($kennelId);

            Log::info('Kennel operations viewed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
            ]);

            return view('admin.kennel.operations', [
                'operations' => $operations,
                'kennelId'   => $kennelId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Kennel operations load failed', [
                'user_id'   => $request->user()?->id,
                'kennel_id' => $kennelId,
                'error'     => $e->getMessage(),
            ]);

            return view('admin.kennel.operations', [
                'operations' => [],
                'kennelId'   => $kennelId,
                'error'      => 'A kennel napi működési adatainak betöltése sikertelen.',
            ]);
        }
    }
}
