<?php

namespace App\Policies;

use App\Constants\Roles;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->assigned_user_id || $user->role?->name === Roles::MANAGER;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role?->name === Roles::MANAGER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->assigned_user_id || $user->role?->name === Roles::MANAGER;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }

     /**
     * Determine whether the user can permanently assign the task to a user.
     */
    public function assign(User $user, Task $task): bool
    {
        return $user->role?->name === Roles::MANAGER;
    }

    /**
     * Determine whether the user can update the status of the task.
     */
    public function updateStatus(User $user, Task $task): bool
    {
        if ($user->role->name === Roles::MANAGER) {
            return true;
        }

        return $user->role->name === Roles::USER
            && $task->assigned_user_id === $user->id;
    }

    public function showTask(User $user, Task $task): bool
    {
        if ($user->role?->name === Roles::MANAGER) return true;
        return ($user->role?->name === Roles::USER && $task->assigned_user_id === $user->id);
    }

}