<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductionProcess;

/**
 * CacheService
 * 
 * Centralized service for managing application-wide caching strategies.
 * Implements cache-aside pattern with automatic invalidation.
 * 
 * Cache Duration Constants:
 * - SHORT: 5 minutes (frequently changing data)
 * - MEDIUM: 1 hour (semi-static data)
 * - LONG: 24 hours (rarely changing data)
 * - WEEK: 7 days (static data)
 */
class CacheService
{
    // Cache duration constants (in minutes)
    const CACHE_SHORT = 5;      // 5 minutes - frequently changing data
    const CACHE_MEDIUM = 60;    // 1 hour - semi-static data
    const CACHE_LONG = 1440;    // 24 hours - rarely changing data
    const CACHE_WEEK = 10080;   // 7 days - static/archived data

    // Cache key prefixes
    const PREFIX_CATEGORIES = 'categories_';
    const PREFIX_PRODUCTS = 'products_';
    const PREFIX_USER = 'user_';
    const PREFIX_ADMIN = 'admin_';

    /**
     * Get all active categories from cache
     * 
     * Re-cached when:
     * - Any category is created/updated/deleted (via CategoryObserver)
     * - Forced invalidation occurs
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCategories()
    {
        $cacheKey = self::PREFIX_CATEGORIES . 'all';

        return Cache::remember($cacheKey, self::CACHE_LONG, function () {
            return Category::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get products by category from cache
     * Useful for category landing pages
     * 
     * @param int $categoryId
     * @param int|null $limit - Null for all products
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getProductsByCategory(int $categoryId, ?int $limit = null)
    {
        $cacheKey = self::PREFIX_PRODUCTS . "category_{$categoryId}";

        return Cache::remember($cacheKey, self::CACHE_MEDIUM, function () use ($categoryId, $limit) {
            $query = Product::where('category_id', $categoryId)
                ->where('is_active', true)
                ->with('category')
                ->orderBy('created_at', 'desc');

            if ($limit) {
                $query->limit($limit);
            }

            return $query->get();
        });
    }

    /**
     * Get featured products from cache
     * Used on home page / dashboard
     * 
     * @param int $limit - Number of featured products to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFeaturedProducts(int $limit = 8)
    {
        $cacheKey = self::PREFIX_PRODUCTS . 'featured';

        return Cache::remember($cacheKey, self::CACHE_MEDIUM, function () use ($limit) {
            return Product::where('is_active', true)
                ->with('category')
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * Get recently added products from cache
     * Used on shop/browse pages
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentProducts(int $limit = 12)
    {
        $cacheKey = self::PREFIX_PRODUCTS . 'recent';

        return Cache::remember($cacheKey, self::CACHE_MEDIUM, function () use ($limit) {
            return Product::where('is_active', true)
                ->with('category')
                ->latest('created_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get single product by slug from cache
     * 
     * @param string $slug
     * @return \App\Models\Product|null
     */
    public static function getProductBySlug(string $slug)
    {
        $cacheKey = self::PREFIX_PRODUCTS . "slug_{$slug}";

        return Cache::remember($cacheKey, self::CACHE_LONG, function () use ($slug) {
            return Product::where('slug', $slug)
                ->where('is_active', true)
                ->with('category')
                ->first();
        });
    }

    /**
     * Get user dashboard summary
     * 
     * Cached with SHORT duration due to frequent updates
     * (user places order, order status changes, etc.)
     * 
     * @param int $userId
     * @return array
     */
    public static function getUserDashboardSummary(int $userId): array
    {
        $cacheKey = self::PREFIX_USER . "dashboard_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($userId) {
            return [
                'pending_orders' => Order::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->count(),

                'in_production_orders' => Order::where('user_id', $userId)
                    ->where('status', 'in_production')
                    ->count(),

                'completed_orders' => Order::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->count(),

                'total_spent' => Order::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->sum('total') ?? 0,

                'average_order_value' => Order::where('user_id', $userId)
                    ->where('status', 'completed')
                    ->avg('total') ?? 0,
            ];
        });
    }

    /**
     * Get user recent orders from cache
     * 
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getUserRecentOrders(int $userId, int $limit = 5)
    {
        $cacheKey = self::PREFIX_USER . "orders_{$userId}";

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () use ($userId, $limit) {
            return Order::where('user_id', $userId)
                ->with(['orderDetails.product', 'payment', 'productionProcesses'])
                ->latest('created_at')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get admin dashboard statistics
     * 
     * Cached with SHORT duration due to real-time data needs
     * 
     * @return array
     */
    public static function getAdminDashboardStats(): array
    {
        $cacheKey = self::PREFIX_ADMIN . 'dashboard_stats';

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () {
            return [
                'total_orders' => Order::count(),

                'pending_orders' => Order::where('status', 'pending')->count(),

                'in_production' => Order::where('status', 'in_production')->count(),

                'completed_today' => Order::whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->count(),

                'revenue_today' => Order::whereDate('completed_at', today())
                    ->where('status', 'completed')
                    ->sum('total') ?? 0,

                'revenue_week' => Order::whereBetween('completed_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])->where('status', 'completed')
                    ->sum('total') ?? 0,

                'revenue_month' => Order::whereMonth('completed_at', now()->month)
                    ->where('status', 'completed')
                    ->sum('total') ?? 0,

                'pending_production' => ProductionProcess::where('status', 'pending')->count(),

                'in_progress_production' => ProductionProcess::where('status', 'in_progress')->count(),

                'total_products' => Product::count(),

                'active_products' => Product::where('is_active', true)->count(),
            ];
        });
    }

    /**
     * Get production dashboard statistics
     * 
     * @return array
     */
    public static function getProductionStats(): array
    {
        $cacheKey = self::PREFIX_ADMIN . 'production_stats';

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () {
            return [
                'total_in_production' => Order::where('status', 'in_production')->count(),

                'by_stage' => ProductionProcess::selectRaw('stage, COUNT(*) as count')
                    ->where('status', '!=', 'completed')
                    ->groupBy('stage')
                    ->pluck('count', 'stage')
                    ->toArray(),

                'overdue_tasks' => ProductionProcess::where('status', '!=', 'completed')
                    ->where('created_at', '<', now()->subDays(7))
                    ->count(),

                'completion_rate' => round(
                    ProductionProcess::where('status', 'completed')->count() /
                    ProductionProcess::count() * 100, 2
                ),
            ];
        });
    }

    /**
     * Get payment statistics
     * 
     * @return array
     */
    public static function getPaymentStats(): array
    {
        $cacheKey = self::PREFIX_ADMIN . 'payment_stats';

        return Cache::remember($cacheKey, self::CACHE_SHORT, function () {
            return [
                'total_paid' => Order::whereHas('payment', function ($q) {
                    $q->where('status', 'completed');
                })->sum('total') ?? 0,

                'pending_payments' => Order::whereHas('payment', function ($q) {
                    $q->where('status', 'pending');
                })->count(),

                'today_payments' => Order::whereHas('payment', function ($q) {
                    $q->whereDate('created_at', today())
                        ->where('status', 'completed');
                })->sum('total') ?? 0,
            ];
        });
    }

    /**
     * Get all cache keys for debugging/monitoring
     * 
     * @return array
     */
    public static function getAllCacheKeys(): array
    {
        return [
            'categories' => self::PREFIX_CATEGORIES . 'all',
            'featured_products' => self::PREFIX_PRODUCTS . 'featured',
            'recent_products' => self::PREFIX_PRODUCTS . 'recent',
            'admin_dashboard' => self::PREFIX_ADMIN . 'dashboard_stats',
            'production_stats' => self::PREFIX_ADMIN . 'production_stats',
            'payment_stats' => self::PREFIX_ADMIN . 'payment_stats',
        ];
    }

    /**
     * ========== CACHE INVALIDATION METHODS ==========
     */

    /**
     * Clear all product-related caches
     * Called when any product is created/updated/deleted
     * 
     * @param int|null $categoryId - If provided, only clears category cache
     */
    public static function clearProductCaches(?int $categoryId = null): void
    {
        // Clear featured and recent products
        Cache::forget(self::PREFIX_PRODUCTS . 'featured');
        Cache::forget(self::PREFIX_PRODUCTS . 'recent');

        // Clear specific category if provided, else clear all
        if ($categoryId) {
            Cache::forget(self::PREFIX_PRODUCTS . "category_{$categoryId}");
        } else {
            // Clear all product category caches (iterate through common IDs)
            for ($i = 1; $i <= 50; $i++) {
                Cache::forget(self::PREFIX_PRODUCTS . "category_{$i}");
                Cache::forget(self::PREFIX_PRODUCTS . "slug_*");
            }
        }

        // Clear admin stats since product count changed
        self::clearAdminCaches();
    }

    /**
     * Clear category caches
     * Called when any category is created/updated/deleted
     */
    public static function clearCategoryCaches(): void
    {
        Cache::forget(self::PREFIX_CATEGORIES . 'all');
        self::clearProductCaches();
    }

    /**
     * Clear user-specific caches
     * Called when user places order or profile changes
     * 
     * @param int $userId
     */
    public static function clearUserCaches(int $userId): void
    {
        Cache::forget(self::PREFIX_USER . "dashboard_{$userId}");
        Cache::forget(self::PREFIX_USER . "orders_{$userId}");
    }

    /**
     * Clear all admin dashboard caches
     * Called when order/payment status changes
     */
    public static function clearAdminCaches(): void
    {
        Cache::forget(self::PREFIX_ADMIN . 'dashboard_stats');
        Cache::forget(self::PREFIX_ADMIN . 'production_stats');
        Cache::forget(self::PREFIX_ADMIN . 'payment_stats');
    }

    /**
     * Clear all application caches
     * Use sparingly - only for major data changes or admin action
     */
    public static function clearAllCaches(): void
    {
        // Clear all categories and products
        self::clearCategoryCaches();
        self::clearProductCaches();

        // Clear all admin/stats caches
        self::clearAdminCaches();

        // Clear user caches (iterate through recent users)
        for ($i = 1; $i <= 100; $i++) {
            Cache::forget(self::PREFIX_USER . "dashboard_{$i}");
            Cache::forget(self::PREFIX_USER . "orders_{$i}");
        }

        // Log cache clear event
        Log::info('All application caches cleared', [
            'timestamp' => now(),
        ]);
    }

    /**
     * Get cache statistics for monitoring
     * 
     * @return array
     */
    public static function getCacheStats(): array
    {
        try {
            // Note: Cache statistics depend on the cache driver
            // Redis provides detailed stats, file-based caches don't
            $cacheStore = Cache::store();

            if (method_exists($cacheStore, 'connection')) {
                $redisStats = $cacheStore->connection()->info('stats');
                $dbSize = $cacheStore->connection()->dbSize();

                return [
                    'cache_hits' => $redisStats['stats']['keyspace_hits'] ?? 0,
                    'cache_misses' => $redisStats['stats']['keyspace_misses'] ?? 0,
                    'hit_rate' => $redisStats['stats']['keyspace_hits'] ?? 0 /
                        (($redisStats['stats']['keyspace_hits'] ?? 0) + ($redisStats['stats']['keyspace_misses'] ?? 0)) * 100,
                    'memory_used' => $redisStats['memory']['used_memory_human'] ?? 'N/A',
                    'total_keys' => $dbSize,
                ];
            }

            // Fallback for non-Redis cache drivers
            return [
                'cache_hits' => 0,
                'cache_misses' => 0,
                'hit_rate' => 0,
                'memory_used' => 'N/A',
                'total_keys' => 0,
                'driver' => config('cache.default'),
                'message' => 'Statistics not available for this cache driver',
            ];
        } catch (\Exception $e) {
            Log::warning('Could not retrieve cache statistics', [
                'error' => $e->getMessage(),
            ]);

            return [
                'cache_hits' => 0,
                'cache_misses' => 0,
                'hit_rate' => 0,
                'memory_used' => 'N/A',
                'total_keys' => 0,
                'error' => 'Redis connection unavailable',
            ];
        }
    }
}
