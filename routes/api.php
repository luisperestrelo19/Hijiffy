<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AvailablityController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);

    Route::post('/availabilities', [AvailablityController::class, 'store']);
});
