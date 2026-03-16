<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

/**
 * Order Observer
 * 
 * Automatically clears relevant caches when orders are created, updated, or deleted
 */
class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $this->clearOrderCaches();
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        $this->clearOrderCaches();
        
        // Clear user-specific order cache if exists
        if ($order->user_id) {
            Cache::forget("user_orders_{$order->user_id}");
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $this->clearOrderCaches();
    }

    /**
     * Clear all order-related caches
     */
    private function clearOrderCaches(): void
    {
        // Dashboard caches (sesuai dengan kunci di DashboardController)
        Cache::forget('dashboard.stats.main');
        Cache::forget('dashboard.chart.revenue');
        Cache::forget('dashboard.top.products');

        // Order statistics (dipakai QueryOptimizationService)
        Cache::forget('order.statistics');
    }
}
