<?php

namespace App\Http\Controllers\Dog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Dog\DogPedigreeService;

class DogPedigreeController extends Controller
{
    public function __construct(
        protected DogPedigreeService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function show(int $dogId, Request $request)
    {
        try {
            $tree = $this->service->getPedigree($dogId);

            Log::info('Dog pedigree viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.dog.pedigree', [
                'tree'  => $tree,
                'dogId' => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Dog pedigree load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.dog.pedigree', [
                'tree'  => [],
                'dogId' => $dogId,
                'error' => 'A pedigré betöltése sikertelen.',
            ]);
        }
    }
}
