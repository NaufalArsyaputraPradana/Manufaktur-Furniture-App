<?php

namespace App\Policies;

use App\Models\ProductionProcess;
use App\Models\User;

class ProductionProcessPolicy
{
    /**
     * Determine if the user can view any production processes
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'production_staff']);
    }

    /**
     * Determine if the user can view a specific production process
     */
    public function view(User $user, ProductionProcess $process): bool
    {
        // Admin can view all processes
        if ($user->role?->name === 'admin') {
            return true;
        }

        // Production staff can view their assigned tasks
        if ($user->role?->name === 'production_staff') {
            return $process->assigned_to === $user->id || $process->assigned_to === null;
        }

        return false;
    }

    /**
     * Determine if the user can create production processes
     */
    public function create(User $user): bool
    {
        return in_array($user->role?->name, ['admin', 'production_staff']);
    }

    /**
     * Determine if the user can update a production process
     */
    public function update(User $user, ProductionProcess $process): bool
    {
        // Admin can update any process
        if ($user->role?->name === 'admin') {
            return true;
        }

        // Production staff can only update their assigned processes
        if ($user->role?->name === 'production_staff') {
            return $process->assigned_to === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can delete a production process
     */
    public function delete(User $user, ProductionProcess $process): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can restore a production process
     */
    public function restore(User $user, ProductionProcess $process): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can permanently delete a production process
     */
    public function forceDelete(User $user, ProductionProcess $process): bool
    {
        return $user->role?->name === 'admin';
    }
}
