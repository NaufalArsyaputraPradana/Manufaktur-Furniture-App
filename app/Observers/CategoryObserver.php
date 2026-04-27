<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\CacheService;

/**
 * CategoryObserver
 * 
 * Automatically invalidates category and product caches when categories change.
 * Ensures all product listings stay fresh when categories are modified.
 * 
 * Events Triggered:
 * - created: New category added
 * - updated: Category details modified (name, description, etc.)
 * - deleted: Category removed
 * - restored: Soft-deleted category restored (if using soft deletes)
 * - forceDeleted: Category permanently deleted
 */
class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     * Clear category cache when new category is added.
     */
    public function created(Category $category): void
    {
        CacheService::clearCategoryCaches();
    }

    /**
     * Handle the Category "updated" event.
     * Clear caches when category details change.
     */
    public function updated(Category $category): void
    {
        CacheService::clearCategoryCaches();
    }

    /**
     * Handle the Category "deleted" event.
     * Clear caches when category is removed.
     */
    public function deleted(Category $category): void
    {
        CacheService::clearCategoryCaches();
    }

    /**
     * Handle the Category "restored" event.
     * Clear caches when soft-deleted category is restored.
     */
    public function restored(Category $category): void
    {
        CacheService::clearCategoryCaches();
    }

    /**
     * Handle the Category "force deleted" event.
     * Clear caches when category is permanently deleted.
     */
    public function forceDeleted(Category $category): void
    {
        CacheService::clearCategoryCaches();
    }
}
