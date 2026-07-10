<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tasks.view');
    }

    public function view(User $user, Task $task): bool
    {
        if (! $user->can('tasks.view')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Sales Manager'])) {
            return true;
        }

        return (int) $task->assigned_to === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('tasks.create');
    }

    public function update(User $user, Task $task): bool
    {
        if (! $user->can('tasks.edit')) {
            return false;
        }

        if ($user->hasAnyRole(['Super Admin', 'Admin', 'Sales Manager'])) {
            return true;
        }

        return (int) $task->assigned_to === (int) $user->id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->can('tasks.delete');
    }
}
