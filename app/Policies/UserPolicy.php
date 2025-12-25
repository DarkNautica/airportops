<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    private function hasSpatie(User $user): bool
    {
        return method_exists($user, 'hasRole') && method_exists($user, 'can');
    }

    private function isAdmin(User $user): bool
    {
        return $this->hasSpatie($user) ? $user->hasRole('Admin') : false;
    }

    private function perm(User $user, string $permission): bool
    {
        return $this->hasSpatie($user) ? $user->can($permission) : false;
    }

    public function viewAny(User $user): bool
    {
        return $this->perm($user, 'users.view') || $this->isAdmin($user);
    }

    public function view(User $user, User $target): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->perm($user, 'users.create') || $this->isAdmin($user);
    }

    public function update(User $user, User $target): bool
    {
        // prevent self-demotion edge cases if you want later
        return $this->perm($user, 'users.update') || $this->isAdmin($user);
    }

    public function delete(User $user, User $target): bool
    {
        // Hard rule: never let a user delete themselves
        if ((int)$user->id === (int)$target->id) {
            return false;
        }

        return $this->perm($user, 'users.delete') || $this->isAdmin($user);
    }
}
