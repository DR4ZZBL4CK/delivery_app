<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\PaqueteController;
use App\Http\Controllers\Api\CamioneroController;
use App\Http\Controllers\Api\CamionController;

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

// Health-check
Route::get('health-check', function () {
    return response()->json([
        'status' => 'OK',
        'version' => config('app.version', '1.0'),
        'timestamp' => now(),
    ]);
});

// Auth (Sanctum)
Route::post('auth/register', [ApiAuthController::class, 'register']);
Route::post('auth/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [ApiAuthController::class, 'logout']);
    Route::get('auth/me', [ApiAuthController::class, 'me']);

    // Paquetes CRUD
    Route::apiResource('paquetes', PaqueteController::class);
    
    // Camioneros CRUD
    Route::apiResource('camioneros', CamioneroController::class);
    
    // Camiones CRUD
    Route::apiResource('camiones', CamionController::class);
});
