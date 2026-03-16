<?php

namespace App\Observers;

use App\Models\ProductionProcess;
use Illuminate\Support\Facades\Cache;

/**
 * ProductionProcess Observer
 * 
 * Automatically clears relevant caches when production processes change
 */
class ProductionProcessObserver
{
    /**
     * Handle the ProductionProcess "created" event.
     */
    public function created(ProductionProcess $productionProcess): void
    {
        $this->clearProductionCaches();
    }

    /**
     * Handle the ProductionProcess "updated" event.
     */
    public function updated(ProductionProcess $productionProcess): void
    {
        $this->clearProductionCaches();
    }

    /**
     * Handle the ProductionProcess "deleted" event.
     */
    public function deleted(ProductionProcess $productionProcess): void
    {
        $this->clearProductionCaches();
    }

    /**
     * Clear all production-related caches
     */
    private function clearProductionCaches(): void
    {
        // Clear production statistics
        Cache::forget('production.statistics');
    }
}
