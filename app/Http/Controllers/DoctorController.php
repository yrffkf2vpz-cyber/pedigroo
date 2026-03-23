<?php

namespace App\Http\Controllers;

use App\Services\Doctor\DoctorService;

class DoctorController extends Controller
{
    public function index(DoctorService $service)
    {
        return view('admin.doctors.index', [
            'doctors' => $service->all(),
        ]);
    }
}
