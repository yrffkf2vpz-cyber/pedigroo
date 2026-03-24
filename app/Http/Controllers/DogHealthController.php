<?php

namespace App\Http\Controllers;

use App\Services\Dog\DogHealthService;

class DogHealthController extends Controller
{
    public function show(int $dogId, DogHealthService $service)
    {
        return view('admin.dog.health', [
            'records' => $service->getForDog($dogId),
            'dogId'   => $dogId,
        ]);
    }
}
