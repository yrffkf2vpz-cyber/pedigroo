<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Vet\VetVisitService;

class VetVisitController extends Controller
{
    public function __construct(
        protected VetVisitService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $dogId, Request $request)
    {
        try {
            $visits = $this->service->getForDog($dogId);

            Log::info('Vet visits viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.vet.visits', [
                'visits' => $visits,
                'dogId'  => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Vet visits load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.vet.visits', [
                'visits' => [],
                'dogId'  => $dogId,
                'error'  => 'Az állatorvosi vizitek betöltése sikertelen.',
            ]);
        }
    }
}
