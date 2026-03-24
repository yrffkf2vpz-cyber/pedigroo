<?php

namespace App\Http\Controllers;

use App\Services\Dog\DogPedigreeService;

class DogPedigreeController extends Controller
{
    public function show(int $dogId, DogPedigreeService $service)
    {
        return view('admin.dog.pedigree', [
            'tree'  => $service->getPedigree($dogId),
            'dogId' => $dogId,
        ]);
    }
}
