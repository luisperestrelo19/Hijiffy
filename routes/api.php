<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailablityController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/availabilities', [AvailablityController::class, 'store']);
    Route::get('/availabilities', [AvailablityController::class, 'index']);
});
