<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Club\JudgeService;

class JudgeController extends Controller
{
    public function __construct(
        protected JudgeService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $judges = $this->service->all();

            Log::info('Judges list viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.judges.index', [
                'judges' => $judges,
            ]);
        } catch (\Throwable $e) {
            Log::error('Judges list load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.judges.index', [
                'judges' => [],
                'error'  => 'A bírók betöltése sikertelen.',
            ]);
        }
    }
}
