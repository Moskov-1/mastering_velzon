<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Frontend\PaymentController;


Route::group(['middleware' => 'auth:api'],function () {
    Route::post('/payments/create-intent', [PaymentController::class, 'createIntent']);
    Route::post('/payments/confirm', [PaymentController::class, 'confirm']);
});

// Webhook route (without auth)
Route::post('/webhook/stripe', [PaymentController::class, 'webhook']);