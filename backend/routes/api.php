<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::delete('/auth/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);

    Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index']);

    Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index']);
    Route::post('/projects', [\App\Http\Controllers\ProjectController::class, 'store']);
    Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show']);
    Route::put('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update']);

    Route::get('/tasks', [\App\Http\Controllers\TaskController::class, 'index']);
    Route::post('/tasks', [\App\Http\Controllers\TaskController::class, 'store']);
    Route::put('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update']);
    Route::patch('/tasks/{task}/category', [\App\Http\Controllers\TaskController::class, 'updateCategory']);
    Route::delete('/tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy']);
});
