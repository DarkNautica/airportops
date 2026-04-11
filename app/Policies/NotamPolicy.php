<?php

namespace App\Policies;

use App\Models\Notam;
use App\Models\User;

class NotamPolicy
{
    private function isAdmin(User $user): bool
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('Admin') || $user->hasRole('Ops Supervisor');
        }

        return false;
    }

    private function canPerm(User $user, string $perm): bool
    {
        if (method_exists($user, 'can')) {
            return (bool) $user->can($perm);
        }

        return false;
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'notams.view');
    }

    public function view(User $user, Notam $notam): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'notams.view');
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'notams.create');
    }

    public function update(User $user, Notam $notam): bool
    {
        if ((bool) $notam->is_locked) {
            return false;
        }

        return $this->isAdmin($user) || $this->canPerm($user, 'notams.update');
    }

    public function delete(User $user, Notam $notam): bool
    {
        if ((bool) $notam->is_locked) {
            return false;
        }

        return $this->isAdmin($user) && $this->canPerm($user, 'notams.delete');
    }

    public function activate(User $user, Notam $notam): bool
    {
        if ((bool) $notam->is_locked) {
            return false;
        }

        return $this->isAdmin($user) || $this->canPerm($user, 'notams.update');
    }

    public function cancel(User $user, Notam $notam): bool
    {
        if ((bool) $notam->is_locked) {
            return false;
        }

        return $this->isAdmin($user) || $this->canPerm($user, 'notams.update');
    }

    public function lock(User $user, Notam $notam): bool
    {
        return $this->isAdmin($user);
    }

    public function unlock(User $user, Notam $notam): bool
    {
        return $this->isAdmin($user);
    }
}
