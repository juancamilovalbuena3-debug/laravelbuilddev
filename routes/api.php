<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VehiculoController;

// Rutas protegidas - requieren token
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/vehiculos', [VehiculoController::class, 'index']);
    Route::post('/vehiculos', [VehiculoController::class, 'store']);
    Route::delete('/vehiculos/{id}', [VehiculoController::class, 'destroy']);
});