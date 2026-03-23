<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Vet\MedicationService;

class MedicationController extends Controller
{
    public function __construct(
        protected MedicationService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $dogId, Request $request)
    {
        try {
            $medications = $this->service->getForDog($dogId);

            Log::info('Medications viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.vet.medications', [
                'medications' => $medications,
                'dogId'       => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Medications load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.vet.medications', [
                'medications' => [],
                'dogId'       => $dogId,
                'error'       => 'A gyógyszerelési adatok betöltése sikertelen.',
            ]);
        }
    }
}
