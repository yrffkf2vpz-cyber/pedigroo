<?php

namespace App\Http\Controllers;

use App\Models\PendingDog;
use App\Services\DogService;
use Illuminate\Http\Request;

class PendingDogController extends Controller
{
    public function index()
    {
        $pendingDogs = PendingDog::where('current_owner_id', auth()->id())
            ->where('activation_status', 'pending')
            ->get();

        return response()->json($pendingDogs);
    }

    public function update(Request $request, PendingDog $pendingDog)
    {
        $this->authorize('update', $pendingDog);

        $pendingDog->update($request->all());

        return response()->json([
            'message' => 'A kutya adatai frissítve.',
            'dog' => $pendingDog
        ]);
    }

    public function activate(PendingDog $pendingDog, DogService $service)
    {
        $this->authorize('activate', $pendingDog);

        $publicDog = $service->movePendingToPublic($pendingDog);

        return response()->json([
            'message' => 'A kutya sikeresen publikálva.',
            'dog' => $publicDog
        ]);
    }
}