<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Api\FrontendController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Documentación API
Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger.index');
})->name('api.documentation');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rutas protegidas
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // API Frontend routes
    Route::prefix('api')->group(function () {
        Route::post('/login', [FrontendController::class, 'login'])->name('api.login');
        Route::post('/logout', [FrontendController::class, 'logout'])->name('api.logout');
        Route::get('/paquetes', [FrontendController::class, 'getPaquetes'])->name('api.paquetes');
        Route::get('/camioneros', [FrontendController::class, 'getCamioneros'])->name('api.camioneros');
        Route::get('/camiones', [FrontendController::class, 'getCamiones'])->name('api.camiones');
    });
});
