<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\DriverController;
use App\Http\Controllers\V1\RideController;

Route::prefix('drivers')->group(function () {

    Route::post('/', [DriverController::class, 'store']);
    Route::get('/', [DriverController::class, 'listDrivers']);
//    Route::get('{id}', [DriverController::class, 'show']);
//    Route::put('{id}', [DriverController::class, 'update']);
    Route::delete('{id}', [DriverController::class, 'destroy']);
    Route::get('/{id}/get-rides', [DriverController::class, 'getOpenRides']);

});

Route::prefix('rides')->group(function () {

    Route::get('/without-driver', [RideController::class, 'listRidesWithoutDriver']);
    Route::post('/request-driver', [RideController::class, 'requestDriver']);

    Route::get('/{id}', [RideController::class, 'show']);
    Route::delete('/{id}', [RideController::class, 'destroy']);
    Route::post('/{id}/cancel-ride', [RideController::class, 'cancelRide']);
    Route::post('/{id}/accept-ride', [RideController::class, 'acceptRide']);
    Route::post('/{id}/refuse-ride', [RideController::class, 'refuseRide']);
    Route::post('/{id}/finish-ride', [RideController::class, 'finishRide']);

    // Forces the estimate ride to be processed immediately
    Route::post('/{id}/estimate-ride', [RideController::class, 'estimateRide']);
    // Get the ride estimate
    Route::get('/{id}/estimate-ride', [RideController::class, 'getRideEstimate']);

});