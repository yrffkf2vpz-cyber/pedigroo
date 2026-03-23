<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Vet\HealthCertificateService;

class HealthCertificateController extends Controller
{
    public function __construct(
        protected HealthCertificateService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(int $dogId, Request $request)
    {
        try {
            $certificates = $this->service->getForDog($dogId);

            Log::info('Health certificates viewed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
            ]);

            return view('admin.vet.certificates', [
                'certificates' => $certificates,
                'dogId'        => $dogId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Health certificates load failed', [
                'user_id' => $request->user()?->id,
                'dog_id'  => $dogId,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.vet.certificates', [
                'certificates' => [],
                'dogId'        => $dogId,
                'error'        => 'Az egészségügyi igazolások betöltése sikertelen.',
            ]);
        }
    }
}
