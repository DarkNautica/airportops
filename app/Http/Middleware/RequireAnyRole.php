<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireAnyRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        if (!$user->hasAnyRoleSafe($roles)) {
            abort(403, 'Insufficient permissions.');
        }

        return $next($request);
    }
}
