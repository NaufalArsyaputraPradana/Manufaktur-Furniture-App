<?php

namespace App\Policies;

use App\Models\ProductionSchedule;
use App\Models\User;

class ProductionSchedulePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ProductionSchedule $schedule): bool
    {
        return $schedule->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ProductionSchedule $schedule): bool
    {
        return $schedule->user_id === $user->id;
    }

    public function delete(User $user, ProductionSchedule $schedule): bool
    {
        return $schedule->user_id === $user->id;
    }

    public function restore(User $user, ProductionSchedule $schedule): bool
    {
        return $schedule->user_id === $user->id;
    }

    public function forceDelete(User $user, ProductionSchedule $schedule): bool
    {
        return false;
    }
}

