<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;

Route::post('register', [UsuarioController::class, 'register']);
Route::post('login', [UsuarioController::class, 'login']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:api')->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::get('/usuarios/{id}', [UsuarioController::class, 'usuarios']);
    Route::get('/users', [UsuarioController::class, 'users']);
});

// Otras rutas de la API...
