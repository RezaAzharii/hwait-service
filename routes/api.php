<?php

use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api', RoleMiddleware::class . ':admin')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::post('recommendations', [RecommendationController::class, 'store']);
    Route::put('recommendations/{id}', [RecommendationController::class, 'update']);
    Route::delete('recommendations/{id}', [RecommendationController::class, 'destroy']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('recommendations', [RecommendationController::class, 'index']);
    Route::get('recommendations/{id}', [RecommendationController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
});