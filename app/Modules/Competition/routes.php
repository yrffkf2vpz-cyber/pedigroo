<?php

use App\Modules\Competition\Controllers\CompetitionController;
use App\Modules\Competition\Controllers\CompetitionAdminController;
use App\Modules\Competition\Controllers\CompetitionEntryAdminController;
use App\Modules\Competition\Controllers\CompetitionVoteAdminController;

Route::prefix('admin/competition/votes')->group(function () {
    Route::get('/', [CompetitionVoteAdminController::class, 'index']);
    Route::delete('/{vote}', [CompetitionVoteAdminController::class, 'destroy']);
});


Route::prefix('admin/competition/entries')->group(function () {
    Route::get('/', [CompetitionEntryAdminController::class, 'index']);
    Route::delete('/{entry}', [CompetitionEntryAdminController::class, 'destroy']);
});


Route::prefix('admin/competitions')->group(function () {
    Route::get('/', [CompetitionAdminController::class, 'index']);
    Route::post('/', [CompetitionAdminController::class, 'store']);
    Route::put('/{competition}', [CompetitionAdminController::class, 'update']);
    Route::post('/{competition}/finish', [CompetitionAdminController::class, 'finish']);
});


Route::prefix('competitions')->group(function () {
    Route::get('/', [CompetitionController::class, 'index']);
    Route::post('/{competition}/enter', [CompetitionController::class, 'enter']);
    Route::post('/vote/{entry}', [CompetitionController::class, 'vote']);
    Route::get('/{competition}/results', [CompetitionController::class, 'results']);
});
