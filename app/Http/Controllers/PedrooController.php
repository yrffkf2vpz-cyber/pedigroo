<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\PedrooSystemCheckService;
use App\Services\PedrooPipelineService;
use App\Services\PedrooCommandInterpreter;

class PedrooController extends Controller
{
    /**
     * Rendszerellenőrzés (Tasks mappa, DB struktúra, fájlok)
     */
    public function systemCheck(PedrooSystemCheckService $service)
    {
        try {
            $result = $service->run();

            Log::info('System check completed', [
                'user_id' => auth()->id(),
            ]);

            return back()->with('status', [
                'message' => 'Rendszerellenőrzés lefutott.',
                'details' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('System check failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return back()->with('status', [
                'message' => 'Hiba történt a rendszerellenőrzés során.',
            ]);
        }
    }

    /**
     * Pipeline státusz lekérése (Dashboard)
     */
    public function pipelineStatus(PedrooPipelineService $service)
    {
        try {
            $status = $service->status();

            Log::info('Pipeline status checked', [
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'status' => 'ok',
                'data'   => $status,
            ]);
        } catch (\Throwable $e) {
            Log::error('Pipeline status failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Pipeline státusz lekérése sikertelen.',
            ], 500);
        }
    }

    /**
     * Master Plan futtatása (NAGY RUN)
     */
    public function pipelineRun(PedrooPipelineService $service)
    {
        try {
            $result = $service->runMasterPlan();

            Log::info('Master Plan run started', [
                'user_id' => auth()->id(),
            ]);

            return back()->with('status', [
                'message' => 'Master Plan futtatása elindítva.',
                'details' => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Master Plan run failed', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
            ]);

            return back()->with('status', [
                'message' => 'Hiba történt a Master Plan futtatása közben.',
            ]);
        }
    }

    /**
     * Természetes nyelvű Pedroo parancs
     */
    public function command(Request $request, PedrooCommandInterpreter $interpreter)
    {
        $data = $request->validate([
            'command' => 'required|string|min:3',
        ]);

        try {
            $result = $interpreter->handle($data['command']);

            Log::info('Pedroo command executed', [
                'user_id' => auth()->id(),
                'command' => $data['command'],
            ]);

            return response()->json([
    'status' => 'ok',
    'message' => 'Parancs feldolgozva.',
    'details' => $result,
]);
        } catch (\Throwable $e) {
            Log::error('Pedroo command failed', [
                'user_id' => auth()->id(),
                'command' => $data['command'],
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
    'status' => 'error',
    'message' => 'Hiba történt a parancs feldolgozása során.',
    'error' => $e->getMessage(),
], 500);;
        }
    }
}
