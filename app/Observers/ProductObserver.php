<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

/**
 * Product Observer
 * 
 * Automatically clears relevant caches when products are created, updated, or deleted
 */
class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearProductCaches($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearProductCaches($product);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearProductCaches($product);
    }

    /**
     * Clear all product-related caches
     */
    private function clearProductCaches(Product $product): void
    {
        // Clear product list caches
        Cache::forget('active_products');
        
        // Clear category product count
        Cache::forget('active_categories_with_count');
        
    // Clear related products cache for this category (tanpa tagging)
    Cache::forget("products_category_{$product->category_id}");
        
        // Pattern matching to clear related product caches
        $this->forgetPattern("related_products_{$product->category_id}_*");
    }

    /**
     * Forget cache keys matching a pattern
     * 
     * Note: Uses simple loop for file-based cache. 
     * For production with Redis, use Cache::tags() or scan() method.
     */
    private function forgetPattern(string $pattern): void
    {
        for ($i = 1; $i <= 100; $i++) {
            Cache::forget(str_replace('*', $i, $pattern));
        }
    }
}
