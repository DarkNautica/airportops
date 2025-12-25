<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use App\Http\Middleware\RequireAnyRole;

use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // ✅ Middleware aliases (Laravel 12+ lives here)
        $middleware->alias([
            // Custom
            'role.any' => RequireAnyRole::class,

            // Spatie Permission
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);

        /**
         * ✅ Optional but recommended:
         * Ensure session + auth middleware are present for web routes.
         * If you used Breeze, this is usually already correct.
         * If you ever see auth/session weirdness, we can explicitly define middleware groups.
         */
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
