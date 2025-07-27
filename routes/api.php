<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailablityController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:guest')->group(function () {
    Route::post('/register', [RegisterController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::middleware('auth:sanctum', 'throttle:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/availabilities', [AvailablityController::class, 'store'])->name('availabilities.post')->middleware('throttle:sync');
    Route::get('/availabilities', [AvailablityController::class, 'index'])->name('availabilities.get');
});
