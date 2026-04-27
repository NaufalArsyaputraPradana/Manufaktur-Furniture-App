<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductionProcess;
use App\Models\ProductionSchedule;
use App\Models\ProductionTodo;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ProductionProcessPolicy;
use App\Policies\ProductionSchedulePolicy;
use App\Policies\ProductionTodoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Order::class              => OrderPolicy::class,
        Payment::class            => PaymentPolicy::class,
        Product::class            => ProductPolicy::class,
        ProductionProcess::class  => ProductionProcessPolicy::class,
        ProductionTodo::class     => ProductionTodoPolicy::class,
        ProductionSchedule::class => ProductionSchedulePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}

