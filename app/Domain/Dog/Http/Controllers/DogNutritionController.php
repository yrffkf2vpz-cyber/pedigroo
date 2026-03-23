<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogNutritionService;

class DogNutritionController extends Controller
{
    public function __construct(
        protected DogNutritionService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $nutrition = $this->service->getForDog($dogId);

            Log::info('Dog nutrition viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.nutrition', [
                'nutrition' => $nutrition,
                'dogId'     => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog nutrition load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.nutrition', [
                'nutrition' => [],
                'dogId'     => $dogId,
                'error'     => 'A táplálkozási adatok betöltése sikertelen.',
            ]);
        }
    }
}
