<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\PaymentController;
use App\Http\Controllers\Api\ReportController;

// API Routes

// Midtrans Webhook (Server-to-Server, no CSRF needed)
Route::post('/payment/midtrans/notification', [PaymentController::class, 'handleNotification'])
    ->name('api.payment.midtrans.notification');

// Generate Snap Token (with session middleware to access auth()->user())
Route::post('/payment/midtrans/token/{order}', [PaymentController::class, 'generateSnapToken'])
    ->middleware(['web'])
    ->name('api.payment.midtrans.token');

// Authenticated API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Report API Routes
    Route::apiResource('reports', ReportController::class);
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('api.reports.sales');
    Route::get('/reports/production', [ReportController::class, 'productionReport'])->name('api.reports.production');
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport'])->name('api.reports.inventory');
    Route::get('/reports/financial', [ReportController::class, 'financialReport'])->name('api.reports.financial');
    Route::get('/reports/{report}/export', [ReportController::class, 'export'])->name('api.reports.export');
});
