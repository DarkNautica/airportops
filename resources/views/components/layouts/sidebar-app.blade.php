<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Clear139') }}</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@100..900&family=Geist+Mono:wght@100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .font-geist       { font-family: 'Geist', ui-sans-serif, system-ui, sans-serif; }
        .font-geist-mono  { font-family: 'Geist Mono', ui-monospace, monospace; }
        .font-instrument  { font-family: 'Instrument Serif', serif; }

        .sidebar-glow {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: .4; }
        }
        .animate-pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
    </style>
</head>

<body class="font-geist antialiased bg-[#F5F7FA] h-screen overflow-hidden">
<div class="h-screen flex overflow-hidden">

    {{-- ── SIDEBAR ── --}}
    <aside class="w-[240px] bg-[#0E1520] hidden md:flex flex-col h-screen shrink-0 relative overflow-hidden">

        {{-- Subtle blue glow --}}
        <div class="sidebar-glow"></div>

        {{-- Logo --}}
        <div class="px-5 pt-6 pb-4 shrink-0">
            <div class="flex items-baseline">
                <span class="font-geist-mono font-medium text-white tracking-wider text-lg">CLEAR</span>
                <span class="font-geist-mono font-medium text-[#F59E0B] tracking-wider text-lg">139</span>
            </div>
            <div class="font-geist-mono text-[10px] text-white/50 uppercase tracking-widest mt-0.5">Part 139 Platform</div>
        </div>

        {{-- Airport chip --}}
        <div class="mx-4 mb-5 rounded-lg bg-white/[0.04] border border-white/[0.06] px-4 py-3">
            <div class="text-[#2563EB] font-bold text-sm font-geist-mono tracking-wide">KAVL</div>
            <div class="text-[11px] text-white/50 mt-0.5">Asheville Regional</div>
            <div class="flex items-center gap-1.5 mt-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-pulse-dot absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] text-white/40 font-geist-mono">System Online</span>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="px-3 flex-1 min-h-0 overflow-y-auto space-y-7">
            @php
                /**
                 * Nav helper.
                 * Supports either a single route name (exact) or a wildcard like "admin.users.*"
                 */
                $navItem = function(string $routeOrPattern, string $label) {
                    $active = request()->routeIs($routeOrPattern);

                    $activeClass = $active
                        ? 'border-l-2 border-[#2563EB] bg-white/[0.04] text-white pl-3'
                        : 'border-l-2 border-transparent text-white/50 hover:text-white/80 hover:bg-white/[0.03] pl-3';

                    // If it's a wildcard, we need an actual route for the href.
                    $isWildcard = str_contains($routeOrPattern, '*');
                    $hrefRoute = $isWildcard ? rtrim($routeOrPattern, '.*') . '.index' : $routeOrPattern;

                    // Only render if the route exists (prevents crashes while features are being built)
                    if (!\Illuminate\Support\Facades\Route::has($hrefRoute)) {
                        return '';
                    }

                    return '<a href="'.route($hrefRoute).'" class="flex items-center h-10 rounded-r-md text-[13px] font-medium transition-colors '.$activeClass.'">'.$label.'</a>';
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

            {{-- OPERATIONS --}}
            <div>
                <div class="font-geist-mono text-[10px] text-white/40 uppercase tracking-widest px-3 mb-2">Operations</div>
                {!! $navItem('dashboard', 'Dashboard') !!}
                {!! $navItem('work-orders.*', 'Work Orders') !!}
                {!! $navItem('inspections.*', 'Inspections') !!}
                @if($canSeePassAlongs)
                    {!! $navItem('pass-alongs.*', 'Pass Alongs') !!}
                @endif
            </div>

            {{-- COMPLIANCE --}}
            @if($canSeeNotams || $canSeeAudit)
                <div>
                    <div class="font-geist-mono text-[10px] text-white/40 uppercase tracking-widest px-3 mb-2">Compliance</div>
                    @if($canSeeNotams)
                        {!! $navItem('notams.*', 'NOTAMs') !!}
                    @endif
                    @if($canSeeAudit)
                        {!! $navItem('audit-logs.*', 'Audit Logs') !!}
                    @endif
                </div>
            @endif

            {{-- ADMIN --}}
            @if($canSeeAdmin)
                <div>
                    <div class="font-geist-mono text-[10px] text-white/40 uppercase tracking-widest px-3 mb-2">Admin</div>
                    {!! $navItem('admin.users.*', 'Users') !!}
                </div>
            @endif
        </nav>

        {{-- User card at bottom --}}
        <div class="mt-auto border-t border-white/[0.06] px-4 py-4 shrink-0 relative z-10">
            <div class="flex items-center gap-3">
                {{-- Initials circle --}}
                <div class="w-10 h-10 rounded-full bg-[#2563EB]/20 text-[#2563EB] flex items-center justify-center text-sm font-semibold shrink-0">
                    @php
                        $initials = '';
                        if ($user?->name) {
                            $parts = explode(' ', $user->name);
                            $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? '', 0, 1));
                        }
                    @endphp
                    {{ $initials ?: '—' }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-[13px] text-white font-medium truncate">{{ $user?->name ?? '—' }}</div>
                    <div class="font-geist-mono text-[10px] text-white/40 mt-0.5">
                        {{ $user?->getRoleNames()?->first() ?? 'User' }}
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="text-[12px] text-white/30 hover:text-white/60 transition-colors font-medium">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN ── --}}
    <div class="flex-1 flex flex-col h-screen overflow-hidden min-w-0">

        {{-- TOP BAR --}}
        <header class="bg-white border-b border-[#E5E7EB] sticky top-0 z-40 shrink-0">
            <div class="h-14 px-6 flex items-center justify-between gap-4">
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
                               class="inline-flex items-center px-3 py-1.5 rounded-md bg-[#0E1520] text-white text-[13px] font-medium hover:bg-[#1a2435] transition-colors">
                                + Inspection
                            </a>
                        @endif

                        @if(\Illuminate\Support\Facades\Route::has('work-orders.create'))
                            <a href="{{ route('work-orders.create') }}"
                               class="inline-flex items-center px-3 py-1.5 rounded-md border border-[#E5E7EB] bg-white text-[#0E1520] text-[13px] font-medium hover:bg-gray-50 transition-colors">
                                + Work Order
                            </a>
                        @endif
                    @endisset
                </div>
            </div>
        </header>

        {{-- ONLY scroll container --}}
        <main class="flex-1 overflow-y-auto min-w-0 bg-[#F5F7FA]">
            <div class="w-full min-w-0 p-6">
                {{ $slot }}
            </div>
        </main>
    </div>

</div>
@stack('scripts')
</body>
</html>
