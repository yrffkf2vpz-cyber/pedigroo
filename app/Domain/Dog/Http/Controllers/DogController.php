<?php

namespace App\Http\Controllers;

use App\Models\Dog;
use App\Services\DogService;
use Illuminate\Http\Request;

class DogController extends Controller
{
    public function update(Request $request, Dog $dog)
    {
        $this->authorize('update', $dog);

        $dog->update($request->all());

        return response()->json([
            'message' => 'A kutya adatai frissítve.',
            'dog' => $dog
        ]);
    }

    public function unpublish(Dog $dog, DogService $service)
    {
        $this->authorize('unpublish', $dog);

        $pending = $service->unpublishDog($dog, auth()->user());

        return response()->json([
            'message' => 'A kutya elrejtve a nyilvános adatbázisból.',
            'pending' => $pending
        ]);
    }
}