<?php

namespace App\Http\Controllers;

use App\Services\Dog\DogOwnershipService;

class DogOwnershipController extends Controller
{
    public function show(int $dogId, DogOwnershipService $service)
    {
        return view('admin.dog.ownership', [
            'owners' => $service->getForDog($dogId),
            'dogId'  => $dogId,
        ]);
    }
}
