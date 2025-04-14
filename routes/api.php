<?php

use App\Http\Controllers\Auth\AuthenticationController;
use App\Http\Controllers\Auth\CheckEmailUniqueController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterUserController;
use App\Http\Controllers\Event\EventController;
use App\Http\Controllers\Ticket\BuyTicketController;
use App\Http\Controllers\Ticket\TicketController;
use App\Http\Controllers\User\BookmarkController;
use App\Http\Controllers\User\CheckPaymentMethodUniqueController;
use App\Http\Controllers\User\PaymentMethodController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Public Route for Email Verification
 */
Route::post('/send', [EmailVerificationController::class, 'sendVerifyCode'])
    ->middleware('throttle:1,1');
Route::post('/verify', [EmailVerificationController::class, 'verifySentCode']);

/**
 * Public Route
 */
Route::middleware('throttle:1000,1')->group(
    function () {
        Route::post('/login', [AuthenticationController::class, 'login']);
        Route::post('/register', [RegisterUserController::class, 'register']);
        Route::post('/check-email', [CheckEmailUniqueController::class, 'checkColumnUnique']);
        Route::post('/password-reset', [PasswordResetController::class, 'resetPassword']);
        Route::apiResource('/events.tickets', TicketController::class)
            ->scoped()
            ->only(['index']);
        Route::apiResource('/users', UserController::class)
            ->only(['index']);
    }
);

/**
 * Protected Route
 */
Route::middleware(['auth:sanctum', 'throttle:1000,1'])->group(
    function () {
        Route::post('/password-change', [PasswordResetController::class, '__invoke']);
        Route::post('/logout', [AuthenticationController::class, 'logout']);

        Route::apiResource('/users', UserController::class)
            ->only(['show', 'update', 'destroy']);

        Route::apiResource('/events', EventController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);

        Route::apiResource('/events.tickets', TicketController::class)
            ->scoped()
            ->only(['store', 'update', 'destroy']);

        Route::get('/users/{user}/events', [UserController::class, 'allEvents']);

        Route::apiResource('/users.payment-methods', PaymentMethodController::class)
            ->scoped()
            ->only(['index', 'store', 'show', 'update', 'destroy']);
        Route::post('/check-card', [CheckPaymentMethodUniqueController::class, 'checkCreditCardNumberUnique']);

        Route::apiResource('/users.bookmarks', BookmarkController::class)
            ->scoped()
            ->only(['index', 'store', 'show']);
        Route::delete('/users/{user}/bookmarks/{event_id}', [BookmarkController::class, 'destroy']);

        Route::apiResource('/users.buy-tickets', BuyTicketController::class)
            ->scoped()
            ->only(['index', 'show',]);
        Route::delete('/users/{user}/scan-tickets', [BuyTicketController::class, 'destroy']);

        Route::apiResource('/events.buy-tickets', BuyTicketController::class)
            ->scoped()
            ->only(['store', 'update', 'destroy']);
    }
);
