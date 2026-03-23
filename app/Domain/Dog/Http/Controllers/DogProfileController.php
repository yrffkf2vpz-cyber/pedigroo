<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use App\Services\Dog\DogProfileService;

class DogProfileController extends Controller
{
    protected DogProfileService $service;

    public function __construct(DogProfileService $service)
    {
        $this->service = $service;
    }

    public function show(int $dogId)
    {
        try {
            return response()->json([
                'data' => $this->service->getProfile($dogId),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 404);
        }
    }
}