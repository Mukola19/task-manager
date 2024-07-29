<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any-tasks');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $this->isOwnerOrHasPermission($user, $task, 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-task');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        return $this->isOwnerOrHasPermission($user, $task, 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return $this->isOwnerOrHasPermission($user, $task, 'delete');
    }

    /**
     * Check if the user is the owner of the task or has a specific permission.
     */
    private function isOwnerOrHasPermission(User $user, Task $task, string $permission): bool
    {
        if ($user->can("{$permission}-own-task")) {
            return $user->id === $task->user_id;
        }

        return $user->can("{$permission}-any-task");
    }
}
