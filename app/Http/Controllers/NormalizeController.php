<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Normalizers\NormalizePipelineService;

class NormalizeController extends Controller
{
    public function __construct(
        protected NormalizePipelineService $pipeline
    ) {
        // ha belső API: auth + admin
        $this->middleware(['auth', 'can:admin']);
    }

    /**
     * POST /api/normalize/dog
     * Bemenet: nyers kutya rekord
     * Kimenet: teljes normalizált kutya rekord
     */
    public function normalizeDog(Request $request): JsonResponse
    {
        $data = $request->validate([
            'debug' => 'nullable|boolean',
            // a többi mezőt a pipeline kezeli, ezért itt nem szigorítjuk
        ]);

        $debug = $data['debug'] ?? false;
        $input = $request->all();

        try {
            $result = $this->pipeline->process($input, $debug);

            Log::info('Dog normalization completed', [
                'user_id' => $request->user()?->id,
                'debug'   => $debug,
            ]);

            return response()->json([
                'status' => 'ok',
                'data'   => $result,
            ]);

        } catch (\Throwable $e) {

            Log::error('Dog normalization failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Hiba történt a normalizálás során.',
            ], 500);
        }
    }
}
