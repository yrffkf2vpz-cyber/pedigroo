<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Admin\DocumentService;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentService $service
    ) {
        $this->middleware(['auth', 'can:admin']);
    }

    public function index(Request $request)
    {
        try {
            $documents = $this->service->all();

            Log::info('Documents list viewed', [
                'user_id' => $request->user()?->id,
            ]);

            return view('admin.documents.index', [
                'documents' => $documents,
            ]);
        } catch (\Throwable $e) {
            Log::error('Documents list load failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return view('admin.documents.index', [
                'documents' => [],
                'error'     => 'A dokumentumok betöltése sikertelen.',
            ]);
        }
    }
}
