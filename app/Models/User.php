<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\RoleName;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'title',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ============================
    // Display helpers
    // ============================

    public function displayTitle(): string
    {
        return $this->title ?: '—';
    }

    // ============================
    // Role helpers (Spatie-first)
    // ============================

    public function hasRoleSafe(string $role): bool
    {
        if (method_exists($this, 'hasRole')) {
            return $this->hasRole($role);
        }

        return ($this->role ?? RoleName::OPS_STAFF) === $role;
    }

    public function hasAnyRoleSafe(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRoleSafe($role)) {
                return true;
            }
        }
        return false;
    }

    public function resolveRoleSafe(): string
    {
        if (method_exists($this, 'getRoleNames')) {
            $roles = $this->getRoleNames()->toArray();

            if (in_array(RoleName::ADMIN, $roles)) return RoleName::ADMIN;
            if (in_array(RoleName::OPS_SUPERVISOR, $roles)) return RoleName::OPS_SUPERVISOR;
            if (in_array(RoleName::OPS_STAFF, $roles)) return RoleName::OPS_STAFF;
            if (in_array(RoleName::VIEWER, $roles)) return RoleName::VIEWER;

            return RoleName::OPS_STAFF;
        }

        return $this->role ?? RoleName::OPS_STAFF;
    }

    /**
     * Hierarchy check:
     * Admin > Ops Supervisor > Ops Staff > Viewer
     */
    public function hasAtLeastRole(string $role): bool
    {
        $rank = [
            RoleName::VIEWER         => 1,
            RoleName::OPS_STAFF      => 2,
            RoleName::OPS_SUPERVISOR => 3,
            RoleName::ADMIN          => 4,
        ];

        $current = $this->resolveRoleSafe();

        return ($rank[$current] ?? 0) >= ($rank[$role] ?? 999);
    }

    // Convenience flags
    public function isAdmin(): bool
    {
        return $this->hasRoleSafe(RoleName::ADMIN);
    }

    public function isOpsSupervisor(): bool
    {
        return $this->hasRoleSafe(RoleName::OPS_SUPERVISOR);
    }

    public function isOpsStaff(): bool
    {
        return $this->hasRoleSafe(RoleName::OPS_STAFF);
    }
}
