<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogBehaviourService;

class DogBehaviourController extends Controller
{
    public function __construct(
        protected DogBehaviourService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $behaviours = $this->service->getForDog($dogId);

            Log::info('Dog behaviour viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.behaviour', [
                'behaviours' => $behaviours,
                'dogId'      => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog behaviour load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.behaviour', [
                'behaviours' => [],
                'dogId'      => $dogId,
                'error'      => 'A viselkedési adatok betöltése sikertelen.',
            ]);
        }
    }
}
