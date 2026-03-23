<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dev\FsController;
use App\Http\Controllers\ConsoleController;
use App\Http\Controllers\PipelineController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BreedRuleController;

// ÚJ CONTROLLEREK
use App\Http\Controllers\PendingDogController;
use App\Http\Controllers\DogController;
use App\Http\Controllers\CronController;

/*
|--------------------------------------------------------------------------
| Public + Dev Routes
|--------------------------------------------------------------------------
*/

Route::get('/dev/fs/list', [FsController::class, 'list']);

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/pedroo/pipeline-status', [\App\Http\Controllers\PipelineDashboardController::class, 'index'])
        ->name('pedroo.pipeline-status');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| User Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Pedroo Console API (backend)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    Route::post('/console/run', [ConsoleController::class, 'run'])
        ->name('console.run');

    Route::post('/pedroo/system-check', [\App\Http\Controllers\PedrooController::class, 'systemCheck'])
        ->name('pedroo.system-check');

    Route::post('/pedroo/pipeline-run', [PipelineController::class, 'run'])
        ->name('pedroo.pipeline-run');

    Route::post('/pedroo/copilot/run', [\App\Http\Controllers\PedrooCopilotController::class, 'run'])
        ->name('pedroo.copilot.run');

    Route::get('/pedroo/pipeline/status-json', function () {
        $status = \App\Models\PipelineTask::where('status', 'pending')->exists()
            ? 'running'
            : 'idle';

        return response()->json(['status' => $status]);
    })->name('pedroo.pipeline-status-json');

    Route::post('/api/pipeline/run', function () {
        return app(PipelineController::class)->run(request());
    })->name('pipeline.legacy');

    /*
    |--------------------------------------------------------------------------
    | Pedroo Breeding Rules Admin
    |--------------------------------------------------------------------------
    */

    Route::prefix('admin/breeds/{breed}')->group(function () {
        Route::get('/rules', [BreedRuleController::class, 'index'])
            ->name('admin.breeds.rules.index');

        Route::post('/rules', [BreedRuleController::class, 'store'])
            ->name('admin.breeds.rules.store');

        Route::delete('/rules/{rule}', [BreedRuleController::class, 'destroy'])
            ->name('admin.breeds.rules.destroy');

        Route::post('/rules/generate-defaults', [BreedRuleController::class, 'generateDefaults'])
            ->name('admin.breeds.rules.generate-defaults');
    });

    /*
    |--------------------------------------------------------------------------
    | Pending & Dog Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/pending-dogs', [PendingDogController::class, 'index']);
    Route::put('/pending-dogs/{pendingDog}', [PendingDogController::class, 'update']);
    Route::post('/pending-dogs/{pendingDog}/activate', [PendingDogController::class, 'activate']);

    Route::put('/dogs/{dog}', [DogController::class, 'update']);
    Route::post('/dogs/{dog}/unpublish', [DogController::class, 'unpublish']);
});

/*
|--------------------------------------------------------------------------
| Pedroo dokumentáció oldalak
|--------------------------------------------------------------------------
*/

Route::get('/pedroo/internal-reminder', fn() => 'Internal Reminder oldal ide kerül majd.')
    ->name('pedroo.internal-reminder');

Route::get('/pedroo/master-plan', fn() => 'Master Plan oldal ide kerül majd.')
    ->name('pedroo.master-plan');

Route::view('/pedroo', 'pedroo');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Pedroo Full HTML Export Viewer (AI számára)
|--------------------------------------------------------------------------
*/

Route::get('/pedroo-full-export/{path}', function ($path) {
    $fullPath = storage_path('pedroo/full-export/' . $path);

    if (!file_exists($fullPath)) {
        abort(404);
    }

    return response()->file($fullPath);
})->where('path', '.*');

/*
|--------------------------------------------------------------------------
| Pedroo Admin (React SPA) – CATCH-ALL (LEGVÉGÉN KELL LENNIE!)
|--------------------------------------------------------------------------
*/

Route::get('/pedroo-console/{any}', function () {
    return file_get_contents(public_path('pedroo-admin/index.html'));
})->where('any', '.*');