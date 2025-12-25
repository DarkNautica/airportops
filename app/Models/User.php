<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Enums\RoleName;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        'role', // fallback if Spatie ever disabled
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
        // Spatie present
        if (method_exists($this, 'hasRole')) {
            return $this->hasRole($role);
        }

        return ($this->role ?? RoleName::OPS_TECH) === $role;
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
        // Prefer Spatie roles if available
        if (method_exists($this, 'getRoleNames')) {
            $roles = $this->getRoleNames()->toArray();

            if (in_array(RoleName::ADMIN, $roles)) return RoleName::ADMIN;
            if (in_array(RoleName::OPS_SUPERVISOR, $roles)) return RoleName::OPS_SUPERVISOR;
            if (in_array(RoleName::OPS_TECH, $roles)) return RoleName::OPS_TECH;
            if (in_array('Ops Staff', $roles)) return 'Ops Staff';
            if (in_array('Viewer', $roles)) return 'Viewer';

            return RoleName::OPS_TECH;
        }

        return $this->role ?? RoleName::OPS_TECH;
    }

    /**
     * Hierarchy check:
     * Admin > Ops Supervisor > Ops Staff > Viewer
     */
    public function hasAtLeastRole(string $role): bool
    {
        $rank = [
            'Viewer'           => 1,
            'Ops Staff'        => 2,
            RoleName::OPS_TECH => 2,
            RoleName::OPS_SUPERVISOR => 3,
            RoleName::ADMIN    => 4,
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
        return $this->hasAnyRoleSafe(['Ops Staff', RoleName::OPS_TECH]);
    }
}
