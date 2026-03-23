<?php

use Illuminate\Support\Facades\Route;
use App\Modules\TrustScore\Controllers\TrustScoreController;

/*
|--------------------------------------------------------------------------
| Felhasználói TrustScore route-ok
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])
    ->prefix('trust')
    ->group(function () {

        // Aktuális TrustScore
        Route::get('/score', [TrustScoreController::class, 'score']);

        // TrustScore események
        Route::get('/events', [TrustScoreController::class, 'events']);
    });

/*
|--------------------------------------------------------------------------
| Admin TrustScore route-ok
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'can:admin'])
    ->prefix('admin/trust')
    ->group(function () {

        // Felhasználók TrustScore listája
        Route::get('/users', [TrustScoreController::class, 'adminList']);

        // Egy felhasználó részletes TrustScore nézete
        Route::get('/users/{id}', [TrustScoreController::class, 'adminDetail']);
    });
