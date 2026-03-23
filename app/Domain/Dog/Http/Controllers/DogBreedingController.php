<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogBreedingService;

class DogBreedingController extends Controller
{
    public function __construct(
        protected DogBreedingService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $breeding = $this->service->getForDog($dogId);

            Log::info('Dog breeding viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.breeding', [
                'breeding' => $breeding,
                'dogId'    => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog breeding load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.breeding', [
                'breeding' => [],
                'dogId'    => $dogId,
                'error'    => 'A tenyésztési adatok betöltése sikertelen.',
            ]);
        }
    }
}
