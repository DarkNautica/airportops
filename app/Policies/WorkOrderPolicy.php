<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkOrder;

class WorkOrderPolicy
{
    private function hasSpatie(User $user): bool
    {
        return method_exists($user, 'hasRole') && method_exists($user, 'can');
    }

    private function isAdmin(User $user): bool
    {
        return $this->hasSpatie($user) ? $user->hasRole('Admin') : false;
    }

    private function isSupervisor(User $user): bool
    {
        return $this->hasSpatie($user) ? $user->hasRole('Ops Supervisor') : false;
    }

    private function perm(User $user, string $permission): bool
    {
        return $this->hasSpatie($user) ? $user->can($permission) : false;
    }

    public function viewAny(User $user): bool
    {
        return $this->perm($user, 'workorders.view') || $this->isAdmin($user) || $this->isSupervisor($user);
    }

    public function view(User $user, WorkOrder $workOrder): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $this->perm($user, 'workorders.create') || $this->isAdmin($user) || $this->isSupervisor($user);
    }

    public function update(User $user, WorkOrder $workOrder): bool
    {
        return $this->perm($user, 'workorders.update') || $this->isAdmin($user) || $this->isSupervisor($user);
    }

    public function delete(User $user, WorkOrder $workOrder): bool
    {
        return ($this->isAdmin($user) || $this->isSupervisor($user)) && $this->perm($user, 'workorders.delete');
    }

    public function assign(User $user): bool
    {
        return $this->perm($user, 'workorders.assign') || $this->isAdmin($user) || $this->isSupervisor($user);
    }
}
