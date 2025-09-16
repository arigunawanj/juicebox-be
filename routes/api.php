<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/weather', [WeatherController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);               // Get current user

    // Posts routes (RESTful)
    Route::get('/posts', [PostController::class, 'index']);           // i. GET /api/posts
    Route::get('/posts/{id}', [PostController::class, 'show']);       // ii. GET /api/posts/{id}
    Route::post('/posts', [PostController::class, 'store']);          // iii. POST /api/posts
    Route::patch('/posts/{id}', [PostController::class, 'update']);   // iv. PATCH /api/posts/{id}
    Route::delete('/posts/{id}', [PostController::class, 'destroy']); // v. DELETE /api/posts/{id}

    // Users routes
    Route::get('/users/{id}', [UserController::class, 'show']);       // vi. GET /api/users/{id}
});
