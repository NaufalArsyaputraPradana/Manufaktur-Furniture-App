<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Eloquent\ProductRepository;

/**
 * Repository Service Provider
 * 
 * Binds repository interfaces to their implementations
 * Enables dependency injection of repositories in controllers
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings
     */
    public function register(): void
    {
        // Order Repository
        $this->app->bind(
            OrderRepositoryInterface::class,
            OrderRepository::class
        );

        // Product Repository
        $this->app->bind(
            ProductRepositoryInterface::class,
            ProductRepository::class
        );

        // TODO: Add more repository bindings as they are created
        // Example:
        // $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        // $this->app->bind(ProductionRepositoryInterface::class, ProductionRepository::class);
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        //
    }
}
