<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Query Optimization Service
 * 
 * Provides utilities for query optimization including:
 * - Eager loading helpers
 * - Query caching
 * - Performance monitoring
 * - Query optimization tips
 */
class QueryOptimizationService
{
    /**
     * Cache duration in seconds
     */
    const CACHE_SHORT = 300; // 5 minutes
    const CACHE_MEDIUM = 1800; // 30 minutes
    const CACHE_LONG = 3600; // 1 hour

    /**
     * Get orders with optimized eager loading
     * 
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOptimizedOrders(array $filters = [])
    {
        $query = \App\Models\Order::query()
            ->with([
                'user:id,name,email,phone',
                'orderDetails' => function($query) {
                    $query->with('product:id,name,sku,base_price');
                },
                'payment:id,order_id,payment_status,transaction_id,payment_date'
            ]);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->get();
    }

    /**
     * Get products with optimized eager loading
     * 
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getOptimizedProducts(array $filters = [])
    {
        $query = \App\Models\Product::query()
            ->with(['category:id,name,slug']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->get();
    }

    /**
     * Get production processes with optimized eager loading
     */
    public static function getOptimizedProductionProcesses(array $filters = [])
    {
        $query = \App\Models\ProductionProcess::query()
            ->with([
                'order:id,order_number,status,user_id',
                'order.user:id,name,email',
                'orderDetail:id,order_id,product_id,product_name,quantity',
                'orderDetail.product:id,name,sku',
                'assignedTo:id,name,email',
            ]);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['assigned_to'])) {
            $query->where('assigned_to', $filters['assigned_to']);
        }

        return $query->latest()->get();
    }

    /**
     * Cache expensive query results
     */
    public static function cacheQuery(string $key, int $duration, callable $callback)
    {
        return Cache::remember($key, $duration, $callback);
    }

    /**
     * Get cached order statistics
     * 
     * @return array
     */
    public static function getOrderStatistics(): array
    {
        return self::cacheQuery('order.statistics', self::CACHE_SHORT, function() {
            return [
                'total' => \App\Models\Order::count(),
                'pending' => \App\Models\Order::where('status', 'pending')->count(),
                'confirmed' => \App\Models\Order::where('status', 'confirmed')->count(),
                'in_production' => \App\Models\Order::where('status', 'in_production')->count(),
                'completed' => \App\Models\Order::where('status', 'completed')->count(),
                'cancelled' => \App\Models\Order::where('status', 'cancelled')->count(),
            ];
        });
    }

    /**
     * Get cached production statistics
     * 
     * @return array
     */
    public static function getProductionStatistics(): array
    {
        return self::cacheQuery('production.statistics', self::CACHE_SHORT, function() {
            return [
                'total' => \App\Models\ProductionProcess::count(),
                'pending' => \App\Models\ProductionProcess::where('status', 'pending')->count(),
                'in_progress' => \App\Models\ProductionProcess::where('status', 'in_progress')->count(),
                'completed' => \App\Models\ProductionProcess::where('status', 'completed')->count(),
                'avg_progress' => \App\Models\ProductionProcess::avg('progress_percentage'),
            ];
        });
    }

    /**
     * Get cached inventory statistics
     * 
     * @return array
     */
    public static function getInventoryStatistics(): array
    {
        return self::cacheQuery('inventory.statistics', self::CACHE_MEDIUM, function() {
            return [
                'total_materials' => 0,
                'low_stock' => 0,
                'out_of_stock' => 0,
                'total_value' => 0,
            ];
        });
    }

    /**
     * Clear all cached statistics
     * 
     * @return void
     */
    public static function clearStatisticsCache(): void
    {
        Cache::forget('order.statistics');
        Cache::forget('production.statistics');
        Cache::forget('inventory.statistics');
    }

    /**
     * Enable query logging for debugging
     * 
     * @return void
     */
    public static function enableQueryLog(): void
    {
        DB::enableQueryLog();
    }

    /**
     * Get query log for debugging
     * 
     * @return array
     */
    public static function getQueryLog(): array
    {
        return DB::getQueryLog();
    }

    /**
     * Count queries executed
     * 
     * @return int
     */
    public static function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }

    /**
     * Analyze query for N+1 problems
     * 
     * @param callable $callback
     * @return array
     */
    public static function analyzeQueries(callable $callback): array
    {
        DB::enableQueryLog();
        
        $callback();
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        return [
            'total_queries' => $queryCount,
            'queries' => $queries,
            'has_n_plus_one' => $queryCount > 10, // Basic heuristic
        ];
    }
}
