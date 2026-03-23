<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogEventParticipationService;

class DogEventParticipationController extends Controller
{
    public function __construct(
        protected DogEventParticipationService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $entries = $this->service->getForDog($dogId);

            Log::info('Dog event participation viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.events', [
                'entries' => $entries,
                'dogId'   => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog event participation load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.events', [
                'entries' => [],
                'dogId'   => $dogId,
                'error'   => 'Az esemény részvételi adatok betöltése sikertelen.',
            ]);
        }
    }
}
