<?php

use Illuminate\Support\Facades\Route;
use App\Modules\User\Controllers\UserController;

Route::middleware(['auth:sanctum'])
    ->prefix('admin/users')
    ->group(function () {

        // User lista
        Route::get('/', [UserController::class, 'index']);

        // Egy user megtekintése
        Route::get('/{user}', [UserController::class, 'show']);

        // User módosítása
        Route::put('/{user}', [UserController::class, 'update']);

        // Szerepkörök hozzárendelése
        Route::post('/{user}/roles', [UserController::class, 'assignRoles']);

        // User törlése (ha engedélyezett)
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });
