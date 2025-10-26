<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Payments\PaymentController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use App\Http\Controllers\API\Payments\StripeWebhookController;

Route::middleware('auth:api')->group(function () {
    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::post('/create-intent', [PaymentController::class, 'createPaymentIntent']);
        Route::post('/confirm', [PaymentController::class, 'confirmPayment']);
        Route::post('/{payment}/capture', [PaymentController::class, 'capturePayment']);
        Route::post('/{payment}/cancel', [PaymentController::class, 'cancelPayment']);
        Route::post('/{payment}/refund', [PaymentController::class, 'refundPayment']);
        Route::get('/{payment}', [PaymentController::class, 'show']);
        Route::get('/', [PaymentController::class, 'index']);
    });

    // Payment Method Routes
    // Route::prefix('payment-methods')->group(function () {
    //     Route::post('/', [PaymentMethodController::class, 'store']);
    //     Route::get('/', [PaymentMethodController::class, 'index']);
    //     Route::put('/{paymentMethod}/default', [PaymentMethodController::class, 'setDefault']);
    //     Route::delete('/{paymentMethod}', [PaymentMethodController::class, 'destroy']);
    // });
});

// Webhook route (should be excluded from CSRF protection)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([VerifyCsrfToken::class]);