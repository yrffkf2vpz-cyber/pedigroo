<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogBoardingService;

class DogBoardingController extends Controller
{
    public function __construct(
        protected DogBoardingService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $stays = $this->service->getForDog($dogId);

            Log::info('Dog boarding viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.boarding', [
                'stays' => $stays,
                'dogId' => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog boarding load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.boarding', [
                'stays' => [],
                'dogId' => $dogId,
                'error' => 'A panzió adatok betöltése sikertelen.',
            ]);
        }
    }
}
