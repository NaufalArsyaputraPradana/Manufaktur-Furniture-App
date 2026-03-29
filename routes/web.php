<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Customer\InvoiceController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderTrackingController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\PaymentController as CustomerPaymentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Production\ProductionController;
use App\Http\Controllers\Production\ProductionProcessController;
use App\Http\Controllers\Production\ProductionTodoController;
use App\Http\Controllers\Production\ProductionScheduleController;
use App\Http\Controllers\Production\ShippingMonitoringController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'submitContact')->name('contact.submit');
});

Route::prefix('products')->name('products.')->controller(CustomerProductController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{product:slug}', 'show')->name('show');
    Route::post('/estimate-price', 'estimatePrice')->name('estimate-price');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->middleware('throttle:5,1')->name('login.submit');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register')->middleware('throttle:3,1')->name('register.submit');
});

Route::middleware('guest')->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');



/*
|--------------------------------------------------------------------------
| Customer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer,admin'])->prefix('customer')->name('customer.')->group(function () {
    // Shopping Cart
    Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/add', 'add')->name('add');
        Route::patch('/{itemKey}', 'update')->name('update');
        Route::delete('/{itemKey}', 'remove')->name('remove');
        Route::delete('/', 'clear')->name('clear');
    });

    // Checkout
    Route::prefix('checkout')->name('checkout.')->controller(CheckoutController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/process', 'process')->name('process');
    });

    // Customer Profile
    Route::prefix('profile')->name('profile.')->controller(CustomerProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/update', 'update')->name('update');
        Route::patch('/password', 'updatePassword')->name('password.update');
        Route::delete('/destroy', 'destroy')->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Order Creation Routes (All Authenticated Users)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('customer/orders')->name('customer.orders.')
    ->controller(OrderTrackingController::class)->group(function () {
        Route::get('/custom', 'customOrderForm')->name('custom');
        Route::post('/custom', 'storeCustomOrder')->name('custom.store');
    });

/*
|--------------------------------------------------------------------------
| Customer Order Routes (Customer & Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:customer,admin'])->prefix('customer/orders')->name('customer.orders.')
    ->controller(OrderTrackingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->name('show');
        Route::patch('/{order}/cancel', 'cancel')->name('cancel');
        Route::get('/{order}/payment', 'showPayment')->name('payment');
        Route::post('/{order}/payment', 'processPayment')->name('payment.process');
        Route::get('/{order}/invoice', [InvoiceController::class, 'show'])->name('invoice');
        Route::get('/{order}/invoice/download', [InvoiceController::class, 'download'])->name('invoice.download');
    });

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Profile
    Route::prefix('profile')->name('profile.')->controller(AdminProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::patch('/update', 'update')->name('update');
        Route::patch('/password', 'updatePassword')->name('password.update');
        Route::delete('/destroy', 'destroy')->name('destroy');
    });

    // Resource Routes
    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', AdminProductController::class);
    Route::resource('orders', AdminOrderController::class);

    // Admin Orders actions
    Route::patch('/orders/{order}/shipping', [AdminOrderController::class, 'updateShipping'])->name('orders.shipping');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.update-status');
    // PERBAIKAN: Ubah dari POST menjadi PATCH
    Route::patch('/orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    // Custom Orders
    Route::prefix('custom-orders')->name('custom-orders.')->controller(CustomOrderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{orderDetail}/calculate', 'calculate')->name('calculate');
        Route::post('/{orderDetail}', 'store')->name('store');
    });

    // Bank Accounts Management
    Route::resource('bank-accounts', BankAccountController::class);

    // Payment Verification
    Route::prefix('payments')->name('payments.')->controller(CustomerPaymentController::class)->group(function () {
        Route::get('/pending', 'pendingVerification')->name('pending');
        Route::get('/{payment}', 'show')->name('show');
        Route::post('/{payment}/verify', 'verify')->name('verify');
        Route::post('/{payment}/reject', 'reject')->name('reject');
        Route::post('/{payment}/confirm-final', 'confirmFinalPayment')->name('confirm-final');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/sales', 'sales')->name('sales');
        Route::get('/production', 'production')->name('production');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/profitability', 'profitability')->name('profitability');
        Route::post('/export', 'export')->name('export');
    });

    // Settings
    Route::prefix('settings')->name('settings.')->controller(SettingController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/update', 'update')->name('update');
    });

    // Admin Production Management (jika ada)
    Route::prefix('production')->name('production.')->controller(ProductionController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/{production}/complete', 'complete')->name('complete');
    });
});

/*
|--------------------------------------------------------------------------
| Production Staff Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:production_staff'])->prefix('production')->name('production.')->group(function () {
    Route::get('/dashboard', [ProductionController::class, 'index'])->name('dashboard');

    // Production Monitoring
    Route::prefix('monitoring')->name('monitoring.')->controller(ProductionProcessController::class)->group(function () {
        Route::get('/', 'ordersIndex')->name('orders');
        Route::get('/{order}', 'index')->name('index');
        Route::get('/{order}/create-stages', 'createStages')->name('createStages');
        Route::put('/process/{process}', 'update')->name('update');
        Route::get('/order/{order}', 'showOrder')->name('order.show');
    });

    // Production Process Management
    Route::prefix('processes')->name('processes.')->group(function () {
        Route::controller(ProductionProcessController::class)->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{process}', 'show')->name('show');
            Route::get('/{process}/edit', 'edit')->name('edit');
            Route::put('/{process}', 'update')->name('update');
            Route::delete('/{process}', 'destroy')->name('destroy');
        });

        Route::controller(ProductionController::class)->group(function () {
            Route::post('/{production}/start', 'startProduction')->name('start');
            Route::patch('/{production}/update-stage', 'updateStage')->name('update-stage');
            Route::post('/{production}/complete', 'complete')->name('complete');
            Route::post('/{production}/materials', 'recordMaterialUsage')->name('materials.record');
        });
    });

    // Production Tracking
    Route::prefix('tracking')->name('tracking.')->controller(ProductionProcessController::class)->group(function () {
        Route::get('/', 'ordersIndex')->name('index');
        Route::get('/{process}', 'show')->name('show');
    });

    // Monitoring pengiriman (muat, ekspedisi, dokumen)
    Route::prefix('shipping')->name('shipping.')->controller(ShippingMonitoringController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{order}', 'show')->name('show');
        Route::post('/{order}/logs', 'storeLog')->name('logs.store');
        Route::patch('/{order}/courier', 'updateCourier')->name('courier.update');
    });
});

/*
|--------------------------------------------------------------------------
| Staff Production Personal Tools (To Do & Schedules)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:production_staff'])
    ->prefix('staff-production')
    ->name('staff.production.')
    ->group(function () {
        // To Do List
        Route::resource('todos', ProductionTodoController::class);
        // AJAX status update
        Route::patch('todos/{todo}/status', [ProductionTodoController::class, 'updateStatus'])
            ->name('todos.update-status');

        // Schedules
        Route::resource('schedules', ProductionScheduleController::class);
        // FullCalendar events feed
        Route::get('schedules-events', [ProductionScheduleController::class, 'events'])
            ->name('schedules.events');
        // Export single schedule to .ics
        Route::get('schedules/{schedule}/export-ics', [ProductionScheduleController::class, 'exportIcs'])
            ->name('schedules.export-ics');
    });