<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ConsoleController extends Controller
{
    public function index()
    {
        return view('console.index');
    }

    public function run(Request $request)
    {
        $task = $request->input('task');

        // 1) DIAGNOSZTIKA – külön task
        if ($task === 'diag') {
            Artisan::call('pedroo:diagnose');
            return back()->with('success', 'Diagnosztika lefutott.');
        }

        // 2) Pipeline modul betöltése
        $serviceClass = "\\App\\Pipeline\\Pipeline{$task}Service";

        if (!class_exists($serviceClass)) {
            return back()->with('error', "Pipeline{$task}Service nem található.");
        }

        // Diagnosztika a pipeline előtt
        Artisan::call('pedroo:diagnose');

        // Pipeline modul futtatása
        $service = new $serviceClass();
        $service->handle();

        // Diagnosztika a pipeline után
        Artisan::call('pedroo:diagnose');

        return back()->with('success', "Pipeline {$task} lefutott és validálva.");
    }
}