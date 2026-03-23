<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Device\Controllers\DeviceController;

Route::middleware(['auth:sanctum'])->group(function () {

    // 1) Detect device or request verification
    Route::post('/device/detect', [DeviceController::class, 'detect']);

    // 2) Verify code and add new device
    Route::post('/device/verify', [DeviceController::class, 'verify']);

    // 3) List all devices for the authenticated user
    Route::get('/device/list', [DeviceController::class, 'list']);

    // 4) Set a device as default
    Route::post('/device/{deviceId}/default', [DeviceController::class, 'setDefault']);

    // 5) Delete a device
    Route::delete('/device/{deviceId}', [DeviceController::class, 'delete']);
});
