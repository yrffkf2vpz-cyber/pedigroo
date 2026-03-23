<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AccessControl\Http\Controllers\AccessRequestController;
use App\Modules\AccessControl\Http\Controllers\AccessDecisionController;
use App\Modules\AccessControl\Http\Controllers\AccessVisibilityController;

Route::prefix('access')->group(function () {

    // 1) Új hozzáférési kérelem létrehozása
    Route::post('/request', [AccessRequestController::class, 'create']);

    // 2) Kérelmek listázása kennel tulajdonosnak
    Route::get('/requests/kennel/{kennelId}', [AccessRequestController::class, 'listForKennel']);

    // 3) Kérelmek listázása felhasználónak
    Route::get('/requests/user/{userId}', [AccessRequestController::class, 'listForUser']);

    // 4) Kérelem részletei
    Route::get('/request/{id}', [AccessRequestController::class, 'detail']);

    // 5) Kérelem jóváhagyása (pipák)
    Route::post('/request/{id}/approve', [AccessDecisionController::class, 'approve']);

    // 6) Kérelem elutasítása
    Route::post('/request/{id}/deny', [AccessDecisionController::class, 'deny']);

    // 7) Láthatóság ellenorzése (backend logika)
    Route::get('/can-view', [AccessVisibilityController::class, 'canView']);
    // OWNER OVERRIDE
    Route::post('/override/set', [AccessOverrideController::class, 'set']);
    Route::post('/override/remove', [AccessOverrideController::class, 'remove']);

});