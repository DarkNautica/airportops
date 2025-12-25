<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function show()
    {
        $checks = [];

        // DB check
        try {
            DB::select('select 1');
            $checks['database'] = ['ok' => true];
        } catch (\Throwable $e) {
            $checks['database'] = ['ok' => false, 'error' => $e->getMessage()];
        }

        // Cache check
        try {
            Cache::put('health_check', 'ok', 10);
            $checks['cache'] = ['ok' => Cache::get('health_check') === 'ok'];
        } catch (\Throwable $e) {
            $checks['cache'] = ['ok' => false, 'error' => $e->getMessage()];
        }

        // Storage check
        $checks['storage'] = ['ok' => is_writable(storage_path())];

        $allOk = collect($checks)->every(fn ($c) => ($c['ok'] ?? false) === true);

        return response()->json([
            'ok' => $allOk,
            'app' => config('app.name'),
            'env' => config('app.env'),
            'debug' => (bool) config('app.debug'),
            'laravel' => app()->version(),
            'php' => PHP_VERSION,
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $allOk ? 200 : 503);
    }
}
