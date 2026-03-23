<?php

namespace App\Http\Controllers;

use App\Services\DogService;

class CronController extends Controller
{
    public function autoPublish(DogService $service)
    {
        $count = $service->autoPublish();

        return response()->json([
            'message' => 'Automatikus publikálás lefutott.',
            'published_today' => $count
        ]);
    }
}