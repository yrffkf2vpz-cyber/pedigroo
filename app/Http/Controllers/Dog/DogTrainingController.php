<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogTrainingService;

class DogTrainingController extends Controller
{
    public function __construct(
        protected DogTrainingService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $trainings = $this->service->getForDog($dogId);

            Log::info('Dog training viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.training', [
                'trainings' => $trainings,
                'dogId'     => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog training load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.training', [
                'trainings' => [],
                'dogId'     => $dogId,
                'error'     => 'A tréning adatok betöltése sikertelen.',
            ]);
        }
    }
}
