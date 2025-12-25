<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    private function hasSpatie(User $user): bool
    {
        return method_exists($user, 'hasRole') && method_exists($user, 'can');
    }

    private function isAdmin(User $user): bool
    {
        return $this->hasSpatie($user) ? $user->hasRole('Admin') : false;
    }

    public function manage(User $user): bool
    {
        // Strict: Admin only (and permission if you want)
        if (!$this->isAdmin($user)) return false;

        return $this->hasSpatie($user) ? $user->can('roles.manage') : true;
    }
}
