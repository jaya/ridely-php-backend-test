<?php

use App\Http\Controllers\AuthController;
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
Route::prefix('auth')->middleware(['api'])->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('refresh-token', [AuthController::class, 'refreshToken']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::prefix('v1')->middleware(['api', 'keycloak.jwt'])->group(base_path('routes/api/v1.php'));
Route::prefix('v2')->middleware(['api', 'keycloak.jwt'])->group(base_path('routes/api/v2.php'));



