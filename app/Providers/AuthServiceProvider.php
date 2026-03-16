<?php

namespace App\Providers;

use App\Models\ProductionSchedule;
use App\Models\ProductionTodo;
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

