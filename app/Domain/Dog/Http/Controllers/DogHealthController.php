<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogHealthService;

class DogHealthController extends Controller
{
    public function __construct(
        protected DogHealthService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $records = $this->service->getForDog($dogId);

            Log::info('Dog health viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.health', [
                'records' => $records,
                'dogId'   => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog health load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.health', [
                'records' => [],
                'dogId'   => $dogId,
                'error'   => 'Az egészségügyi adatok betöltése sikertelen.',
            ]);
        }
    }
}
