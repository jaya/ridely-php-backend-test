<?php

use App\Http\Controllers\V1\DriverController;
use App\Http\Controllers\V1\RideController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::prefix('v1')->middleware(['api'])->group(base_path('routes/api/v1.php'));
Route::prefix('v2')->middleware(['api'])->group(base_path('routes/api/v2.php'));



