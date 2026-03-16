<?php

namespace App\Policies;

use App\Models\ProductionTodo;
use App\Models\User;

class ProductionTodoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ProductionTodo $todo): bool
    {
        return $todo->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ProductionTodo $todo): bool
    {
        return $todo->user_id === $user->id;
    }

    public function delete(User $user, ProductionTodo $todo): bool
    {
        return $todo->user_id === $user->id;
    }

    public function restore(User $user, ProductionTodo $todo): bool
    {
        return $todo->user_id === $user->id;
    }

    public function forceDelete(User $user, ProductionTodo $todo): bool
    {
        return false;
    }
}

