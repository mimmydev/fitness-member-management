<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful API routes for the Fitness Centre Member Management System.
| All routes are prefixed with /api (configured in bootstrap/app.php).
| Routes are organized by resource and follow Laravel conventions.
|
*/

// Public authentication routes with rate limiting
Route::prefix('auth')->group(function () {
    // Register: 3 attempts per 10 minutes to prevent spam account creation
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,10')
        ->name('auth.register');

    // Login: 5 attempts per minute to prevent brute force attacks
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('auth.login');
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {

    // Authentication routes (authenticated users only)
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('auth.logout');

        Route::post('/logout-all', [AuthController::class, 'logoutAllDevices'])
            ->name('auth.logoutAll');

        Route::get('/me', [AuthController::class, 'me'])
            ->name('auth.me');
    });

    // Member profile resource routes
    // Follows RESTful conventions:
    // GET    /api/members          -> index()
    // POST   /api/members          -> store()
    // GET    /api/members/{id}     -> show()
    // PUT    /api/members/{id}     -> update()
    // PATCH  /api/members/{id}     -> update()
    // DELETE /api/members/{id}     -> destroy()
    Route::apiResource('members', MemberController::class);
});
