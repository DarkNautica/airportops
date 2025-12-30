<?php

namespace App\Providers;

use App\Models\Inspection;
use App\Models\WorkOrder;
use App\Models\User;
use App\Models\AuditLog;
use App\Policies\InspectionPolicy;
use App\Policies\WorkOrderPolicy;
use App\Policies\UserPolicy;
use App\Policies\AuditLogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Inspection::class => InspectionPolicy::class,
        WorkOrder::class  => WorkOrderPolicy::class,
        User::class       => UserPolicy::class,
        AuditLog::class   => AuditLogPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
