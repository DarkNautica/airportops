<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Commands
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


/*
|--------------------------------------------------------------------------
| Scheduled Tasks (Laravel 11)
|--------------------------------------------------------------------------
|
| These are executed by:
|   php artisan schedule:run
| which should be triggered every minute by system cron.
|
| FAA relevance:
| - Continuous verification of audit log integrity
| - Detects tampering immediately
|
*/

Schedule::command('audit:verify-chain')
    ->dailyAt('02:00')
    ->appendOutputTo(storage_path('logs/audit_chain.log'))
    ->onFailure(function () {
        logger()->critical(
            'AUDIT CHAIN FAILURE — CRYPTOGRAPHIC INTEGRITY VIOLATION DETECTED'
        );
    });
