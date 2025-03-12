<?php

use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterUserController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthenticationController::class, 'login']);
Route::post('/logout', [AuthenticationController::class, 'logout'])
    ->middleware('auth:sanctum');
Route::post('/register', [RegisterUserController::class, 'register']);
Route::post('/password-reset', [PasswordResetController::class, '__invoke'])
    ->middleware('auth:sanctum');

Route::apiResource('/user', UserController::class)
    ->only(['show', 'update', 'destroy'])
    ->middleware('auth:sanctum');

Route::apiResource('/user', UserController::class)
    ->only(['show', 'index']);

Route::apiResource('/event', EventController::class)
    ->only(['index', 'show']);

Route::apiResource('/event', EventController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware('auth:sanctum');
