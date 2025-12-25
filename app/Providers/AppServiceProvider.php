<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Inspection;
use App\Observers\InspectionObserver;
use App\Models\WorkOrder;
use App\Observers\WorkOrderObserver;
use App\Models\PassAlong;
use App\Observers\PassAlongObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inspection::observe(InspectionObserver::class);
        WorkOrder::observe(WorkOrderObserver::class);
        PassAlong::observe(PassAlongObserver::class);
    }
}
