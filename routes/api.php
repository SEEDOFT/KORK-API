<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterUserController;
use Illuminate\Support\Facades\Route;

/**
 * Public Route
 */
Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/register', [RegisterUserController::class, 'register']);
Route::post('/check-email', [RegisterUserController::class, 'checkColumnUnique']);
Route::apiResource('/events', EventController::class)
    ->only(['index']);
Route::apiResource('/events.tickets', TicketController::class)
    ->only(['index']);
Route::apiResource('/users', UserController::class)
    ->only(['index']);

/**
 * Protected Route
 */
Route::middleware('auth:sanctum')->group(
    function () {
        Route::post('/password-reset', [PasswordResetController::class, '__invoke']);
        Route::post('/logout', [AuthenticationController::class, 'logout']);

        Route::apiResource('/users', UserController::class)
            ->only(['show', 'update', 'destroy']);

        Route::apiResource('/payment-method', PaymentMethodController::class)
            ->only(['store', 'show', 'update', 'destroy']);

        Route::apiResource('/events', EventController::class)
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('/events.tickets', TicketController::class)
            ->only(['store', 'update', 'destroy']);
    }
);
