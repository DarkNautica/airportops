<?php

namespace App\Policies;

use App\Models\PassAlong;
use App\Models\User;

class PassAlongPolicy
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
        return $this->isAdmin($user) || $this->canPerm($user, 'passalongs.view');
    }

    public function view(User $user, PassAlong $passAlong): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'passalongs.view');
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'passalongs.create');
    }

    public function update(User $user, PassAlong $passAlong): bool
    {
        if ((bool) $passAlong->is_locked) {
            return false;
        }

        return $this->isAdmin($user) || $this->canPerm($user, 'passalongs.update');
    }

    public function submit(User $user, PassAlong $passAlong): bool
    {
        if ($passAlong->status === 'submitted') {
            return false;
        }

        return $this->isAdmin($user) || $this->canPerm($user, 'passalongs.update');
    }

    public function unlock(User $user, PassAlong $passAlong): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, PassAlong $passAlong): bool
    {
        if ((bool) $passAlong->is_locked) {
            return false;
        }

        return $this->isAdmin($user) && $this->canPerm($user, 'passalongs.delete');
    }

    public function export(User $user, PassAlong $passAlong): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'passalongs.export');
    }
}
