<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use App\Services\Dog\DogChampionshipService;

class DogChampionshipController extends Controller
{
    protected DogChampionshipService $service;

    public function __construct(DogChampionshipService $service)
    {
        $this->service = $service;
    }

    public function list(int $dogId)
    {
        return response()->json([
            'data' => $this->service->getForDog($dogId),
        ]);
    }
}