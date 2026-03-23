<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\Ingest\IngestExcelService;
use App\Services\Ingest\IngestApiService;
use App\Services\Ingest\IngestScraperService;
use App\Services\Ingest\IngestPdfService;

class IngestController extends Controller
{
    public function __construct()
    {
        // ingest műveletek csak admin/superadmin számára
        $this->middleware(['auth', 'can:admin']);
    }

    public function excel(Request $request, IngestExcelService $excel): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:20480', // 20MB
        ]);

        try {
            $excel->import($data['file']->getRealPath());

            Log::info('Excel ingest completed', [
                'user_id' => $request->user()?->id,
                'filename' => $data['file']->getClientOriginalName(),
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            Log::error('Excel ingest failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Excel ingest failed.',
            ], 500);
        }
    }

    public function api(Request $request, IngestApiService $api): JsonResponse
    {
        $data = $request->validate([
            'dogs' => 'required|array',
        ]);

        try {
            $api->import($data['dogs']);

            Log::info('API ingest completed', [
                'user_id' => $request->user()?->id,
                'count'   => count($data['dogs']),
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            Log::error('API ingest failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'API ingest failed.',
            ], 500);
        }
    }

    public function scraper(Request $request, IngestScraperService $scraper): JsonResponse
    {
        $data = $request->validate([
            'dogs' => 'required|array',
        ]);

        try {
            $scraper->import($data['dogs']);

            Log::info('Scraper ingest completed', [
                'user_id' => $request->user()?->id,
                'count'   => count($data['dogs']),
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            Log::error('Scraper ingest failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Scraper ingest failed.',
            ], 500);
        }
    }

    public function pdf(Request $request, IngestPdfService $pdf): JsonResponse
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:pdf|max:20480', // 20MB
        ]);

        try {
            $pdf->import($data['file']->getRealPath());

            Log::info('PDF ingest completed', [
                'user_id' => $request->user()?->id,
                'filename' => $data['file']->getClientOriginalName(),
            ]);

            return response()->json(['status' => 'ok']);
        } catch (\Throwable $e) {
            Log::error('PDF ingest failed', [
                'user_id' => $request->user()?->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'PDF ingest failed.',
            ], 500);
        }
    }
}
