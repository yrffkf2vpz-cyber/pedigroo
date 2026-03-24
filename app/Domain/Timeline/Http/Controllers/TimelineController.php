<?php

namespace App\Domain\Timeline\Http\Controllers;

use App\Http\Controllers\Controller;

class TimelineController extends Controller
{
    public function index()
    {
        return response()->json(['status' => 'timeline ok']);
    }
}