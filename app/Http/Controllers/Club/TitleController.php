<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Club\TitleService;

class TitleController extends Controller
{
    public function __construct(
        protected TitleService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $titles = $this->service->all();

            Log::info('Titles list viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.titles.index', [
                'titles' => $titles,
            ]);
        } catch (\Throwable $e) {
            Log::error('Titles list load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.titles.index', [
                'titles' => [],
                'error'  => 'A címek betöltése sikertelen.',
            ]);
        }
    }
}
