<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Invitation\Controllers\InvitationController;

/*
|--------------------------------------------------------------------------
| Admin / belso route-ok
|--------------------------------------------------------------------------
| Ezekhez bejelentkezés szükséges.
*/

Route::middleware(['auth:sanctum'])
    ->prefix('admin/invitations')
    ->group(function () {

        // Meghívók listázása
        Route::get('/', [InvitationController::class, 'index']);

        // Meghívó generálása
        Route::post('/generate', [InvitationController::class, 'generate']);
    });

/*
|--------------------------------------------------------------------------
| Nyilvános route-ok
|--------------------------------------------------------------------------
| Ezeket a meghívott felhasználó használja az e-mailben kapott linkkel.
*/

Route::prefix('invite')
    ->group(function () {

        // Meghívó érvényesítése (token ellenorzés)
        Route::get('/validate', [InvitationController::class, 'validateToken']);

        // Meghívó elfogadása ? regisztráció befejezése
        Route::post('/accept', [InvitationController::class, 'accept']);
    });
