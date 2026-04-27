<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\CacheService;

/**
 * ProductObserver
 * 
 * Automatically invalidates product and category caches when products change.
 * This ensures cached data stays fresh without manual cache management.
 * 
 * Events Triggered:
 * - created: New product added
 * - updated: Existing product modified
 * - deleted: Product removed
 * - restored: Soft-deleted product restored (if using soft deletes)
 * - forceDeleted: Product permanently deleted
 */
class ProductObserver
{
    /**
     * Handle the Product "created" event.
     * Clear all product caches when new product is added.
     */
    public function created(Product $product): void
    {
        CacheService::clearProductCaches($product->category_id);
    }

    /**
     * Handle the Product "updated" event.
     * Clear caches when product details change.
     */
    public function updated(Product $product): void
    {
        CacheService::clearProductCaches($product->category_id);

        // If category changed, also clear old category cache
        if ($product->isDirty('category_id')) {
            CacheService::clearProductCaches($product->getOriginal('category_id'));
        }
    }

    /**
     * Handle the Product "deleted" event.
     * Clear caches when product is removed.
     */
    public function deleted(Product $product): void
    {
        CacheService::clearProductCaches($product->category_id);
    }

    /**
     * Handle the Product "restored" event.
     * Clear caches when soft-deleted product is restored.
     */
    public function restored(Product $product): void
    {
        CacheService::clearProductCaches($product->category_id);
    }

    /**
     * Handle the Product "force deleted" event.
     * Clear caches when product is permanently deleted.
     */
    public function forceDeleted(Product $product): void
    {
        CacheService::clearProductCaches($product->category_id);
    }
}

