<?php

namespace App\Http\Controllers;

use App\Services\Club\ClubService;

class ClubController extends Controller
{
    public function index(ClubService $service)
    {
        return view('admin.clubs.index', [
            'clubs' => $service->all(),
        ]);
    }
}
