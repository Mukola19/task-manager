<?php
namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view-any-categories');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Category $category): bool
    {
        return $this->isOwnerOrHasPermission($user, $category, 'view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create-category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): bool
    {
        if($category->is_static) {
            return false;
        }

        return $this->isOwnerOrHasPermission($user, $category, 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Category $category): bool
    {
        if($category->is_static) {
            return false;
        }

        return $this->isOwnerOrHasPermission($user, $category, 'delete');
    }

    /**
     * Check if the user is the owner of the category or has a specific permission.
     */
    private function isOwnerOrHasPermission(User $user, Category $category, string $permission): bool
    {
        if ($user->can("{$permission}-own-category")) {
            return $user->id === $category->user_id;
        }

        return $user->can("{$permission}-any-category");
    }
}
