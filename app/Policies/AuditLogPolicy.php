<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('audit.view') || $user->can('admin.view');
    }

    public function viewSensitive(User $user): bool
    {
        return $user->can('audit.view_sensitive') || $user->can('admin.view');
    }

    // Immutable: no edits/deletes in app, ever.
    public function update(User $user, AuditLog $log): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $log): bool
    {
        return false;
    }

    public function forceDelete(User $user, AuditLog $log): bool
    {
        return false;
    }

    public function restore(User $user, AuditLog $log): bool
    {
        return false;
    }
}
