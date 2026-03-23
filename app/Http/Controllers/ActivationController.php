<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ActivationService;

class ActivationController extends Controller
{
    /**
     * Aktivál egy ingestelt rekordot (pl. kutya, dokumentum, pipeline elem).
     */
    public function activate($id, ActivationService $service)
    {
        try {
            $result = $service->activate($id);

            Log::info('Activation successful', [
                'id' => $id,
                'result' => $result,
            ]);

            return response()->json([
                'status'  => 'ok',
                'message' => 'Aktiválás sikeres.',
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Activation failed', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Hiba történt az aktiválás során.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}