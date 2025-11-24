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
| Aquí se registran todas las rutas web de la aplicación.
| Estas rutas se cargan por el RouteServiceProvider y utilizan
| el middleware "web".
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// 📘 Documentación de la API (Swagger)
// Usar el controlador de l5-swagger directamente
Route::get('/api/documentation', function (\Illuminate\Http\Request $request) {
    $request->offsetSet('documentation', 'default');
    $configFactory = resolve(\L5Swagger\ConfigFactory::class);
    $config = $configFactory->documentationConfig('default');
    $request->offsetSet('config', $config);
    
    $controller = resolve(\L5Swagger\Http\Controllers\SwaggerController::class);
    return $controller->api($request);
})->name('api.documentation');

// 🔐 RUTAS DE AUTENTICACIÓN (solo para invitados)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// 🌐 API FRONTEND (rutas internas del sistema)
Route::prefix('api')->group(function () {
    // Login para obtener token (accesible para invitados)
    Route::post('/login', [FrontendController::class, 'login'])->name('api.login');

    // Logout y recursos requieren tener token en sesión; opcionalmente podemos proteger con 'auth'
    Route::post('/logout', [FrontendController::class, 'logout'])->name('api.logout');
    Route::get('/paquetes', [FrontendController::class, 'getPaquetes'])->name('api.paquetes');
    Route::get('/camioneros', [FrontendController::class, 'getCamioneros'])->name('api.camioneros');
    Route::get('/camiones', [FrontendController::class, 'getCamiones'])->name('api.camiones');
});

// 🔒 RUTAS PROTEGIDAS (solo usuarios autenticados)
Route::middleware('auth')->group(function () {
    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard general
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 🟢 PANEL DE ADMINISTRACIÓN (solo rol admin)
    Route::get('/admin', [DashboardController::class, 'admin'])
        ->middleware('admin')
        ->name('admin.dashboard');

    // 📦 Listado de Paquetes (vista protegida)
    Route::get('/paquetes', function () {
        return view('paquetes.index');
    })->name('paquetes.index');

    // 🚚 Listado de Camioneros (vista protegida)
    Route::get('/camioneros', function () {
        return view('camioneros.index');
    })->name('camioneros.index');

    // 🚛 Listado de Camiones (vista protegida)
    Route::get('/camiones', function () {
        return view('camiones.index');
    })->name('camiones.index');
});
