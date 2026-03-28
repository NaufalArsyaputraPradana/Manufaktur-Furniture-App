<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\PaymentController;

// API Routes

// Midtrans Webhook (Server-to-Server, no CSRF needed)
Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleNotification'])
    ->name('api.payment.midtrans.notification');

// Generate Snap Token (with session middleware to access auth()->user())
Route::post('/payment/midtrans/token/{order}', [PaymentController::class, 'generateSnapToken'])
    ->middleware(['web'])
    ->name('api.payment.midtrans.token');
