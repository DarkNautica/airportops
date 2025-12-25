<?php

namespace App\Policies;

use App\Models\Inspection;
use App\Models\User;

class InspectionPolicy
{
    /**
     * “Admin” power (and Ops Supervisor) with safe fallbacks.
     * - If Spatie is installed, use roles/permissions.
     * - If not installed, default to false (secure by default).
     */
    private function isAdmin(User $user): bool
    {
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('Admin') || $user->hasRole('Ops Supervisor');
        }

        return false;
    }

    /**
     * Permission helper (works with Spatie; safe fallback).
     */
    private function canPerm(User $user, string $perm): bool
    {
        if (method_exists($user, 'can')) {
            // In a Spatie-enabled app, $user->can('perm.name') works
            return (bool) $user->can($perm);
        }

        return false;
    }

    /**
     * Global shortcut: Admin/Ops Supervisor can do most things.
     * Note: lock checks still happen in each method where needed.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Allow admins/supervisors to bypass normal permission gates,
        // but NOT lock-specific abilities unless explicitly allowed.
        // We still enforce lock in update/delete/certify below.
        if ($this->isAdmin($user)) {
            return null; // keep evaluating per-ability (so lock still blocks)
        }

        return null;
    }

    // -------------------------
    // Resource abilities
    // -------------------------

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'inspections.view');
    }

    public function view(User $user, Inspection $inspection): bool
    {
        // Viewing is allowed if they can view inspections.
        // (If you want “only inspector can view”, tighten it here.)
        return $this->isAdmin($user) || $this->canPerm($user, 'inspections.view');
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user) || $this->canPerm($user, 'inspections.create');
    }

    /**
     * Update = Admin/Ops Supervisor OR the inspector who created it
     * AND user must have inspections.update permission (if using Spatie)
     * BUT NEVER if locked.
     */
    public function update(User $user, Inspection $inspection): bool
    {
        if ((bool) $inspection->is_locked) {
            return false;
        }

        $isOwner = (int) $inspection->inspector_id === (int) $user->id;

        // If Spatie perms exist, require them.
        // Admin/Supervisor can proceed; non-admin must have inspections.update.
        if (method_exists($user, 'can')) {
            if (!($this->isAdmin($user) || $user->can('inspections.update'))) {
                return false;
            }
        }

        return $this->isAdmin($user) || $isOwner;
    }

    /**
     * Delete = Admin/Ops Supervisor only
     * AND inspections.delete permission (if using Spatie)
     * BUT NEVER if locked.
     */
    public function delete(User $user, Inspection $inspection): bool
    {
        if ((bool) $inspection->is_locked) {
            return false;
        }

        if (method_exists($user, 'can') && !$user->can('inspections.delete') && !$this->isAdmin($user)) {
            return false;
        }

        return $this->isAdmin($user);
    }

    // -------------------------
    // Custom abilities
    // -------------------------

    /**
     * Certify = Admin/Ops Supervisor OR inspector who created it
     * AND inspections.update (or a dedicated inspections.certify, if you add it)
     * BUT NEVER if locked.
     */
    public function certify(User $user, Inspection $inspection): bool
    {
        if ((bool) $inspection->is_locked) {
            return false;
        }

        $isOwner = (int) $inspection->inspector_id === (int) $user->id;

        // Permission gate (optional but recommended)
        if (method_exists($user, 'can')) {
            // If you add a dedicated permission later, swap to 'inspections.certify'
            if (!($this->isAdmin($user) || $user->can('inspections.update'))) {
                return false;
            }
        }

        return $this->isAdmin($user) || $isOwner;
    }

    /**
     * Unlock = Admin/Ops Supervisor only (strict)
     * (Optionally require a permission like inspections.unlock)
     */
    public function unlock(User $user, Inspection $inspection): bool
    {
        // If you add a dedicated permission later, enforce it here.
        return $this->isAdmin($user);
    }

    /**
     * Export (print/pdf) = anyone who can view inspections,
     * but you can tighten this to inspections.export if you want.
     */
    public function export(User $user, Inspection $inspection): bool
    {
        if (method_exists($user, 'can')) {
            return $this->isAdmin($user)
                || $user->can('inspections.export')
                || $user->can('inspections.view');
        }

        return $this->isAdmin($user);
    }
}
