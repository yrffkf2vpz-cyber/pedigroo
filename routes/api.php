<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IngestController;
use App\Http\Controllers\DogController;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\NormalizeController;
use App\Http\Controllers\Dog\DogProfileController;
use App\Http\Controllers\Dev\DevFileSystemController;
use App\Http\Controllers\Dev\DevCodePatchController;
use App\Http\Controllers\Dev\DevModuleGeneratorController;
use App\Http\Controllers\Dev\DevDatabaseIntrospectController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\Public\DogTimelineController as PublicDogTimelineController;
use App\Http\Controllers\Api\AiTaskController;
use App\Http\Controllers\PedrooController;

// TIMELINE CONTROLLERS (ÚJ)
use App\Http\Controllers\Timeline\DogTimelineController;
use App\Http\Controllers\Timeline\KennelTimelineController;
use App\Http\Controllers\Timeline\BreederTimelineController;
use App\Http\Controllers\Timeline\BreedTimelineController;
use App\Http\Controllers\Timeline\ClubTimelineController;

// BUYER ACCESS
use App\Http\Controllers\Api\Breeding\BuyerAccessController;
use App\Modules\Breeding\Controllers\BreedingRequestController;
use App\Modules\Breeding\Controllers\BreedingGrantController;



// ---------------------------------------------------------
// PEDROO COMMAND
// ---------------------------------------------------------

Route::post('/pedroo/command', [PedrooController::class, 'command']);


// ---------------------------------------------------------
// DEV DATABASE
// ---------------------------------------------------------

Route::prefix('dev/db')->group(function () {
    Route::get('/tables',        [DevDatabaseIntrospectController::class, 'tables']);
    Route::get('/columns',       [DevDatabaseIntrospectController::class, 'columns']);
    Route::get('/indexes',       [DevDatabaseIntrospectController::class, 'indexes']);
    Route::get('/foreign-keys',  [DevDatabaseIntrospectController::class, 'foreignKeys']);
    Route::get('/exists/table',  [DevDatabaseIntrospectController::class, 'tableExists']);
    Route::get('/exists/column', [DevDatabaseIntrospectController::class, 'columnExists']);
});

Route::post('/dev/module/generate', [DevModuleGeneratorController::class, 'generate']);

Route::prefix('dev/code')->group(function () {
    Route::get('/get',   [DevCodePatchController::class, 'get']);
    Route::post('/patch', [DevCodePatchController::class, 'patch']);
});

Route::prefix('dev/fs')->group(function () {
    Route::get('/list',  [DevFileSystemController::class, 'list']);
    Route::get('/read',  [DevFileSystemController::class, 'read']);
    Route::post('/write', [DevFileSystemController::class, 'write']);
    Route::delete('/delete', [DevFileSystemController::class, 'delete']);
});


// ---------------------------------------------------------
// INGEST
// ---------------------------------------------------------

Route::prefix('ingest')->group(function () {
    Route::post('/excel',   [IngestController::class, 'excel']);
    Route::post('/api',     [IngestController::class, 'api']);
    Route::post('/scraper', [IngestController::class, 'scraper']);
    Route::post('/pdf',     [IngestController::class, 'pdf']);
    Route::post('/activate/{id}', [ActivationController::class, 'activate']);
});


// ---------------------------------------------------------
// NORMALIZE
// ---------------------------------------------------------

Route::prefix('normalize')->group(function () {
    Route::post('/dog', [NormalizeController::class, 'normalizeDog']);
});


// ---------------------------------------------------------
// DOGS
// ---------------------------------------------------------

Route::prefix('dogs')->group(function () {
    Route::get('/search', [DogController::class, 'search']);
    Route::get('/{id}',   [DogController::class, 'show']);

    Route::get('/{dogId}/profile', [DogProfileController::class, 'show']);

    // PUBLIC TIMELINE (régi)
    Route::get('/breeds/{id}/timeline', [PublicDogTimelineController::class, 'breed']);
    Route::get('/dogs/{id}/timeline', [PublicDogTimelineController::class, 'dog']);
    Route::get('/dogs/{id}/timeline/merged', [PublicDogTimelineController::class, 'merged']);
});


// ---------------------------------------------------------
// PEDROO API
// ---------------------------------------------------------

Route::get('/pedroo/review', [\App\Http\Controllers\PedrooReviewApiController::class, 'index']);
Route::get('/pedroo/rules', function () {
    return response()->json(
        json_decode(file_get_contents(config_path('pedroo/rules.json')), true)
    );
});
Route::get('/pedroo/pipeline/{pipeline}',
    [\App\Http\Controllers\PedrooPipelineApiController::class, 'show']
);
Route::get('/pedroo/console/menu',
    [\App\Http\Controllers\PedrooConsoleMenuApiController::class, 'index']
);


// ---------------------------------------------------------
// FILE MANAGER
// ---------------------------------------------------------

Route::prefix('files')->group(function () {
    Route::post('/create-folder', [FileManagerController::class, 'createFolder']);
    Route::post('/create-file', [FileManagerController::class, 'createFile']);
    Route::post('/write-file', [FileManagerController::class, 'writeFile']);
    Route::post('/read-file', [FileManagerController::class, 'readFile']);
    Route::post('/delete-file', [FileManagerController::class, 'deleteFile']);
    Route::post('/list', [FileManagerController::class, 'list']);
});


// ---------------------------------------------------------
// AI PIPELINE TASK
// ---------------------------------------------------------

Route::post('/ai/task', [AiTaskController::class, 'handle']);


// ---------------------------------------------------------
// BUYER ACCESS MODULE (ÚJ)
// ---------------------------------------------------------

Route::prefix('buyer-access')->group(function () {
    Route::post('/request', [BuyerAccessController::class, 'createRequest']);
    Route::post('/{request}/decision', [BuyerAccessController::class, 'decide']);
    Route::get('/grants/buyer/{buyer}', [BuyerAccessController::class, 'listBuyerGrants']);
    Route::get('/requests/kennel/{kennel}', [BuyerAccessController::class, 'listKennelRequests']);
});


// ---------------------------------------------------------
// TIMELINE MODULE (ÚJ, KÜLÖN PREFIX)
// ---------------------------------------------------------

Route::prefix('timeline')->group(function () {

    Route::get('/dog/{id}', [DogTimelineController::class, 'index']);
    Route::post('/dog/{id}', [DogTimelineController::class, 'store']);

    Route::get('/kennel/{id}', [KennelTimelineController::class, 'index']);
    Route::post('/kennel/{id}', [KennelTimelineController::class, 'store']);

    Route::get('/breeder/{id}', [BreederTimelineController::class, 'index']);
    Route::post('/breeder/{id}', [BreederTimelineController::class, 'store']);

    Route::get('/breed/{id}', [BreedTimelineController::class, 'index']);
    Route::post('/breed/{id}', [BreedTimelineController::class, 'store']);

    Route::get('/club/{id}', [ClubTimelineController::class, 'index']);
    Route::post('/club/{id}', [ClubTimelineController::class, 'store']);
});

// Breeding Requests
Route::post('/breeding/request', [BreedingRequestController::class, 'create']);
Route::post('/breeding/request/{id}/approve', [BreedingRequestController::class, 'approve']);
Route::post('/breeding/request/{id}/deny', [BreedingRequestController::class, 'deny']);
Route::get('/breeding/my-requests', [BreedingRequestController::class, 'myRequests']);
Route::get('/breeding/kennel/{kennelId}/requests', [BreedingRequestController::class, 'kennelRequests']);

// Breeding Grants
Route::post('/breeding/grant/{id}/revoke', [BreedingGrantController::class, 'revoke']);
Route::post('/breeding/grant/{id}/expire', [BreedingGrantController::class, 'expire']);



// ---------------------------------------------------------
// ACCESS CONTROL MODULE (ÚJ)
// ---------------------------------------------------------

require __DIR__.'/api/access.php';

