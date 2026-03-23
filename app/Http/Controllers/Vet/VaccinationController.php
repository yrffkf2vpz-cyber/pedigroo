<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Vet\VaccinationService;

class VaccinationController extends Controller
{
    public function __construct(
        protected VaccinationService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $dogId, Request $request)
    {
        try {
            $vaccinations = $this->service->getForDog($dogId);

            Log::info('Vaccinations viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.vet.vaccinations', [
                'vaccinations' => $vaccinations,
                'dogId'        => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Vaccinations load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.vet.vaccinations', [
                'vaccinations' => [],
                'dogId'        => $dogId,
                'error'        => 'Az oltási adatok betöltése sikertelen.',
            ]);
        }
    }
}
