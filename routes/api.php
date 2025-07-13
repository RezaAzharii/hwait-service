<?php

use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgresController;
use App\Http\Controllers\TargetController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api', RoleMiddleware::class . ':admin')->group(function () {
    Route::post('recommendations', [RecommendationController::class, 'store']);
    Route::put('recommendations/{id}', [RecommendationController::class, 'update']);
    Route::delete('recommendations/{id}', [RecommendationController::class, 'destroy']);
});

Route::middleware('auth:api', RoleMiddleware::class . ':saver')->group(function () {
    Route::get('targets', [TargetController::class, 'index']);
    Route::post('targets', [TargetController::class, 'store']);
    Route::put('targets/{id}', [TargetController::class, 'update']);
    Route::delete('targets/{id}', [TargetController::class, 'destroy']);
    Route::get('targets/{id}/progres', [TargetController::class, 'showProgres']);
    Route::get('targets/riwayatTabungan', [TargetController::class, 'riwayatTabungan']);

    Route::post('progres', [ProgresController::class, 'store']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('recommendations', [RecommendationController::class, 'index']);
    Route::get('recommendations/{id}', [RecommendationController::class, 'show']);
    Route::post('/logout', [AuthController::class, 'logout']);
});