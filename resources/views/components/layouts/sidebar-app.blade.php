<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'AirportOps') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-100 h-screen overflow-hidden">
<div class="h-screen flex overflow-hidden">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-gray-900 text-gray-100 hidden md:flex flex-col h-screen overflow-y-auto shrink-0">
        <div class="px-5 py-4 border-b border-white/10 shrink-0">
            <div class="text-lg font-semibold tracking-wide">AirportOps</div>
            <div class="text-xs text-gray-400 mt-1">Part 139 Operations</div>
        </div>

        <nav class="px-3 py-4 space-y-1 text-sm flex-1 min-h-0">
            @php
                /**
                 * Nav helper.
                 * Supports either a single route name (exact) or a wildcard like "admin.users.*"
                 */
                $navItem = function(string $routeOrPattern, string $label) {
                    $active = request()->routeIs($routeOrPattern);

                    $activeClass = $active
                        ? 'bg-white/10 text-white'
                        : 'text-gray-300 hover:text-white hover:bg-white/5';

                    // If it's a wildcard, we need an actual route for the href.
                    $isWildcard = str_contains($routeOrPattern, '*');
                    $hrefRoute = $isWildcard ? rtrim($routeOrPattern, '.*') . '.index' : $routeOrPattern;

                    // Only render if the route exists (prevents crashes while features are being built)
                    if (!\Illuminate\Support\Facades\Route::has($hrefRoute)) {
                        return '';
                    }

                    return '<a href="'.route($hrefRoute).'" class="block px-3 py-2 rounded-md '.$activeClass.'">'.$label.'</a>';
                };

                $user = request()->user();

                // Existing perms
                $canSeeNotams = $user?->can('notams.view') ?? false;
                $canSeeAdmin  = ($user?->can('users.view') ?? false) || ($user?->can('roles.manage') ?? false);

                /**
                 * Audit visibility:
                 * - If you have a policy/permission, use it
                 * - Otherwise, gracefully allow if the route exists AND user is logged in
                 *   (so you can ship the UI before final perms)
                 *
                 * Tighten later by replacing this with a real permission gate.
                 */
                $hasAuditRoute = \Illuminate\Support\Facades\Route::has('audit-logs.index');

                // Preferred: real permission (if you have it)
                $permAudit = $user?->can('audit.view') ?? false;

                // Fallback: let any authenticated user see it if perms not wired yet
                $canSeeAudit = $permAudit || ($hasAuditRoute && $user);

                // Pass Alongs (you can tighten permissions later)
                $hasPassAlongRoute = \Illuminate\Support\Facades\Route::has('pass-alongs.index');
                $permPassAlong = $user?->can('pass-alongs.view') ?? false; // optional future permission
                $canSeePassAlongs = $hasPassAlongRoute && $user; // keep simple + safe for now
            @endphp

            {!! $navItem('dashboard', 'Dashboard') !!}
            {!! $navItem('work-orders.*', 'Work Orders') !!}
            {!! $navItem('inspections.*', 'Inspections') !!}

            {{-- PASS ALONGS --}}
            @if($canSeePassAlongs)
                {!! $navItem('pass-alongs.*', 'Pass Alongs') !!}
            @endif

            {{-- NOTAMs --}}
            @if($canSeeNotams)
                {!! $navItem('notams.*', 'NOTAMs') !!}
            @endif

            {{-- Oversight (Audit Logs) --}}
            @if($canSeeAudit)
                <div class="pt-3 mt-3 border-t border-white/10 text-xs text-gray-400 px-3">
                    Oversight
                </div>
                {!! $navItem('audit-logs.*', 'Audit Logs') !!}
            @endif

            {{-- Admin --}}
            @if($canSeeAdmin)
                <div class="pt-3 mt-3 border-t border-white/10 text-xs text-gray-400 px-3">
                    Admin
                </div>
                {!! $navItem('admin.users.*', 'Users') !!}
            @endif
        </nav>

        {{-- Footer stays reachable because sidebar scrolls internally --}}
        <div class="mt-auto border-t border-white/10 px-5 py-4 shrink-0">
            <div class="text-xs text-gray-400">Signed in as</div>
            <div class="text-sm font-semibold">{{ $user?->name ?? '—' }}</div>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="w-full text-left px-3 py-2 rounded-md bg-white/5 hover:bg-white/10 text-sm">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">

        {{-- TOP BAR --}}
        <header class="bg-white border-b border-gray-200 sticky top-0 z-40 shrink-0">
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>

                {{-- Right side: page actions (preferred) with safe fallback --}}
                <div class="flex items-center gap-3 shrink-0">
                    @isset($actions)
                        {{ $actions }}
                    @else
                        {{-- Fallback actions if page didn't provide actions --}}
                        @if(\Illuminate\Support\Facades\Route::has('inspections.create'))
                            <a href="{{ route('inspections.create') }}"
                               class="inline-flex items-center px-3 py-2 rounded-md bg-gray-900 text-white text-sm hover:bg-black">
                                + Inspection
                            </a>
                        @endif

                        @if(\Illuminate\Support\Facades\Route::has('work-orders.create'))
                            <a href="{{ route('work-orders.create') }}"
                               class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm hover:bg-gray-50">
                                + Work Order
                            </a>
                        @endif
                    @endisset
                </div>
            </div>
        </header>

        {{-- ONLY scroll container --}}
        <main class="flex-1 overflow-y-auto min-w-0">
            <div class="w-full min-w-0 px-6 lg:px-10 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>

</div>
</body>
</html>
