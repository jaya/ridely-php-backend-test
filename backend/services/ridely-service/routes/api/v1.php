<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\DriverController;
use App\Http\Controllers\V1\RideController;

Route::prefix('drivers')->group(function () {

    Route::post('/', [DriverController::class, 'store']);
    Route::get('/', [DriverController::class, 'index']);
//    Route::get('{id}', [DriverController::class, 'show']);
//    Route::put('{id}', [DriverController::class, 'update']);
    Route::delete('{id}', [DriverController::class, 'destroy']);
    Route::get('/{id}/get-rides', [DriverController::class, 'getOpenRides']);

});

Route::prefix('rides')->group(function () {

    Route::get('/{id}', [RideController::class, 'show']);
    Route::delete('/{id}', [RideController::class, 'destroy']);
    Route::post('/{id}/estimate-ride', [RideController::class, 'estimateRide']);
    Route::get('/{id}/estimate-ride', [RideController::class, 'getRidePrice']);
    Route::post('/request-driver', [RideController::class, 'requestDriver']);
    Route::post('/cancel-ride', [RideController::class, 'cancelRide']);
    Route::post('/accept-ride', [RideController::class, 'acceptRide']);
    Route::post('/refuse-ride', [RideController::class, 'refuseRide']);
    Route::post('/finish-ride', [RideController::class, 'finishRide']);

});