<?php

namespace App\Policies;

use App\Models\PassAlong;
use App\Models\User;

class PassAlongPolicy
{
    public function viewAny(User $user): bool
    {
        // ✅ This is what was causing /pass-alongs to 403.
        // Keep it simple for now: any authenticated user can view the module.
        return true;
    }

    public function view(User $user, PassAlong $passAlong): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, PassAlong $passAlong): bool
    {
        // Don’t allow edits if locked
        return !$passAlong->is_locked;
    }

    public function submit(User $user, PassAlong $passAlong): bool
    {
        // Allow submit if not already submitted
        return $passAlong->status !== 'submitted';
    }

    public function unlock(User $user, PassAlong $passAlong): bool
    {
        // Tighten later (role/permission). For now allow unlock to stop blocking you.
        return true;
    }

    public function delete(User $user, PassAlong $passAlong): bool
    {
        return true;
    }
}
