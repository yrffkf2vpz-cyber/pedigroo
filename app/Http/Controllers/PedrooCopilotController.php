<?php

namespace App\Http\Controllers;

use App\Services\Pipeline\PipelineService;
use Illuminate\Http\Request;

class PedrooCopilotController extends Controller
{
    public function run(Request $request, PipelineService $pipeline)
    {
        // 1) Feladatok hozzáadása a pedroo_copilot.blade.php alapján
        $pipeline->addTask('copilot:prepare');
        $pipeline->addTask('copilot:scan');
        $pipeline->addTask('copilot:sync');
        $pipeline->addTask('copilot:optimize');
        $pipeline->addTask('copilot:finalize');

        // 2) Minden task lefuttatása
        while ($pipeline->runNextTask()) {
            // fut, amíg van pending task
        }

        return back()->with('status', 'Pedroo Copilot feladatok lefutottak.');
    }
}