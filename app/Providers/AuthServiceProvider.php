<?php

namespace App\Providers;

use App\Models\Inspection;
use App\Models\WorkOrder;
use App\Models\User;
use App\Policies\InspectionPolicy;
use App\Policies\WorkOrderPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Inspection::class => InspectionPolicy::class,
        WorkOrder::class  => WorkOrderPolicy::class,
        User::class       => UserPolicy::class,
        \App\Models\AuditLog::class => \App\Policies\AuditLogPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
