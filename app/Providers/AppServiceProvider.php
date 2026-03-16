<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductionProcess;
use App\Observers\OrderObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductionProcessObserver;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap 5 pagination globally
        Paginator::useBootstrapFive();

        // Register model observers for automatic cache invalidation
        Order::observe(OrderObserver::class);
        Product::observe(ProductObserver::class);
        ProductionProcess::observe(ProductionProcessObserver::class);
    }
}
