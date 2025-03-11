<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');
Route::post('/password-reset', [AuthController::class, '__invoke'])
    ->middleware('auth:sanctum');

Route::apiResource('/user', UserController::class)
    ->only(['store']);

Route::apiResource('/user', UserController::class)
    ->only(['show', 'update', 'destroy'])
    ->middleware('auth:sanctum');

Route::apiResource('/event', EventController::class)
    ->only(['index', 'show']);

Route::apiResource('/event', EventController::class)
    ->only(['store', 'update', 'destroy'])
    ->middleware('auth:sanctum');
