<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        // Admin role always allowed.
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Otherwise require explicit permission.
        return $user->can('audit.view');
    }

    public function viewSensitive(User $user): bool
    {
        // Admin role always allowed.
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Optional: only if you ever add this permission later.
        return $user->can('audit.view_sensitive');
    }

    // Immutable: no edits/deletes in app, ever.
    public function update(User $user, AuditLog $log): bool { return false; }
    public function delete(User $user, AuditLog $log): bool { return false; }
    public function forceDelete(User $user, AuditLog $log): bool { return false; }
    public function restore(User $user, AuditLog $log): bool { return false; }
}
