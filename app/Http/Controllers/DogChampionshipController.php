<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogChampionshipService;

class DogChampionshipController extends Controller
{
    public function __construct(
        protected DogChampionshipService $service
    ) {
        // csak admin vagy superadmin érheti el
        $this->middleware(['auth', 'can:admin']);
    }

    /**
     * Egy kutya összes championship eredménye
     */
    public function index(int $dogId, Request $request)
    {
        try {
            $results = $this->service->getForDog($dogId);

            Log::info('Dog championships viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.championship.index', [
                'results' => $results,
                'dogId'   => $dogId,
            ]);

        } catch (\Throwable $e) {

            Log::error('Dog championships load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.championship.index', [
                'results' => [],
                'dogId'   => $dogId,
                'error'   => 'A championship adatok betöltése sikertelen.',
            ]);
        }
    }
}

