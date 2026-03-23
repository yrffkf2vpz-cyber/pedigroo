<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Token\Controllers\TokenController;

/*
|--------------------------------------------------------------------------
| Felhasználói token route-ok
|--------------------------------------------------------------------------
| Ezekhez bejelentkezés szükséges.
*/

Route::middleware(['auth:sanctum'])
    ->prefix('tokens')
    ->group(function () {

        // Token egyenleg
        Route::get('/balance', [TokenController::class, 'balance']);

        // Token tranzakciók listázása
        Route::get('/history', [TokenController::class, 'history']);

        // Token örökbeadás (végleges átadás)
        Route::post('/give', [TokenController::class, 'giveToken']);

        // Token kölcsönadás
        Route::post('/loan', [TokenController::class, 'loanToken']);

        // Kölcsön visszafizetése
        Route::post('/repay', [TokenController::class, 'repayLoan']);
    });

/*
|--------------------------------------------------------------------------
| Admin token route-ok (késobb bovítheto)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'can:admin'])
    ->prefix('admin/tokens')
    ->group(function () {

        // Admin jutalom adása (opcionális)
        Route::post('/reward', [TokenController::class, 'adminReward']);

        // Admin költés (opcionális)
        Route::post('/spend', [TokenController::class, 'adminSpend']);
    });
