<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Breeding\BuyerAccessController;

Route::prefix('buyer-access')->group(function () {

    // Buyer létrehoz egy hozzáféréskérést
    Route::post('/request', [BuyerAccessController::class, 'createRequest']);

    // Kennel owner döntést hoz
    Route::post('/{request}/decision', [BuyerAccessController::class, 'decide']);

    // Buyer lekéri a saját grantjeit
    Route::get('/grants/buyer/{buyer}', [BuyerAccessController::class, 'listBuyerGrants']);

    // Kennel lekéri a saját requestjeit
    Route::get('/requests/kennel/{kennel}', [BuyerAccessController::class, 'listKennelRequests']);
});