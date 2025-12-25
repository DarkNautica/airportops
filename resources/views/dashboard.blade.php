<x-sidebar-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">Dashboard</h2>
            <p class="text-sm text-gray-600 mt-1">Operational overview — work orders + Part 139 inspections.</p>
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="hidden sm:flex items-center gap-2">
            <a href="{{ route('inspections.create') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                <span class="text-base leading-none">+</span> Inspection
            </a>

            <a href="{{ route('work-orders.create') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50 shadow-sm">
                <span class="text-base leading-none">+</span> Work Order
            </a>
        </div>
    </x-slot>

    {{-- Bento canvas (darker, layered, non-white) --}}
    <div class="min-h-screen py-10 bg-gradient-to-b from-slate-100 via-gray-100 to-slate-200">
        <div class="w-full px-6 lg:px-10 space-y-8">

            {{-- Alerts (keep, but make it feel like a tile) --}}
            @if ($missingInspectionToday || $overdueCount > 0)
                <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 via-yellow-50 to-white shadow-sm overflow-hidden">
                    <div class="p-5">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5">
                                <div class="h-10 w-10 rounded-xl bg-amber-500/10 border border-amber-200 flex items-center justify-center">
                                    <span class="text-amber-900 font-black">!</span>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="font-semibold text-amber-900">Operational Alerts</div>
                                    <div class="text-xs text-amber-900/60">Action recommended</div>
                                </div>

                                <ul class="list-disc pl-5 text-sm mt-2 space-y-1 text-amber-900">
                                    @if($missingInspectionToday)
                                        <li><span class="font-semibold">Inspection missing:</span> No inspection logged for today.</li>
                                    @endif
                                    @if($overdueCount > 0)
                                        <li><span class="font-semibold">Overdue WOs:</span> {{ $overdueCount }} work order(s) past due date.</li>
                                    @endif
                                </ul>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if($missingInspectionToday)
                                        <a href="{{ route('inspections.create') }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-xl bg-amber-900 text-amber-50 text-xs font-semibold hover:bg-amber-950">
                                            Create Inspection
                                        </a>
                                    @endif
                                    @if($overdueCount > 0)
                                        <a href="{{ route('work-orders.index') }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-xl border border-amber-300 bg-white text-amber-900 text-xs font-semibold hover:bg-amber-50">
                                            Review Work Orders
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="h-1.5 bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-300"></div>
                </div>
            @endif


            @php
                // Utility: tile base classes
                $tileBase = "rounded-2xl border shadow-sm overflow-hidden transition hover:shadow-md";
            @endphp

            {{-- Bento Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 auto-rows-[minmax(180px,auto)]">

                {{-- KPI: Open Work Orders (tinted tile) --}}
                <div class="{{ $tileBase }} lg:col-span-3 bg-gradient-to-br from-blue-50 via-slate-50 to-white border-blue-100">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Open Work Orders</div>
                            <span class="inline-flex items-center rounded-full bg-blue-600/10 text-blue-700 border border-blue-200 px-2 py-0.5 text-xs font-semibold">
                                Ops
                            </span>
                        </div>
                        <div class="mt-3 text-4xl font-black text-gray-900">{{ $openWorkOrdersCount }}</div>
                        <div class="mt-2 text-xs text-gray-600">Not completed.</div>
                    </div>
                    <div class="px-5 py-3 bg-white/60 border-t border-blue-100 flex items-center justify-between text-xs text-gray-600">
                        <span>Open list</span>
                        <a href="{{ route('work-orders.index') }}" class="font-semibold text-blue-700 hover:underline">View</a>
                    </div>
                </div>

                {{-- KPI: Critical Open (more aggressive tint) --}}
                <div class="{{ $tileBase }} lg:col-span-3 bg-gradient-to-br from-rose-50 via-red-50 to-white border-red-100">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Critical Open</div>
                            <span class="inline-flex items-center rounded-full bg-red-600/10 text-red-700 border border-red-200 px-2 py-0.5 text-xs font-semibold">
                                Critical
                            </span>
                        </div>
                        <div class="mt-3 text-4xl font-black text-gray-900">{{ $criticalOpenCount }}</div>
                        <div class="mt-2 text-xs text-gray-600">Priority = Critical.</div>
                    </div>
                    <div class="px-5 py-3 bg-white/60 border-t border-red-100 flex items-center justify-between text-xs text-gray-600">
                        <span>Needs eyes</span>
                        <a href="{{ route('work-orders.index') }}" class="font-semibold text-red-700 hover:underline">Review</a>
                    </div>
                </div>

                {{-- KPI: Overdue --}}
                <div class="{{ $tileBase }} lg:col-span-3 bg-gradient-to-br from-amber-50 via-yellow-50 to-white border-amber-100">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Overdue</div>
                            <span class="inline-flex items-center rounded-full bg-amber-600/10 text-amber-800 border border-amber-200 px-2 py-0.5 text-xs font-semibold">
                                Due
                            </span>
                        </div>
                        <div class="mt-3 text-4xl font-black text-gray-900">{{ $overdueCount }}</div>
                        <div class="mt-2 text-xs text-gray-600">Due date passed.</div>
                    </div>
                    <div class="px-5 py-3 bg-white/60 border-t border-amber-100 flex items-center justify-between text-xs text-gray-600">
                        <span>Resolve</span>
                        <a href="{{ route('work-orders.index') }}" class="font-semibold text-amber-800 hover:underline">Fix</a>
                    </div>
                </div>

                {{-- KPI: Inspections Today --}}
                <div class="{{ $tileBase }} lg:col-span-3 bg-gradient-to-br from-emerald-50 via-green-50 to-white border-emerald-100">
                    <div class="p-5">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Inspections Today</div>
                            <span class="inline-flex items-center rounded-full bg-emerald-600/10 text-emerald-700 border border-emerald-200 px-2 py-0.5 text-xs font-semibold">
                                Part 139
                            </span>
                        </div>
                        <div class="mt-3 text-4xl font-black text-gray-900">{{ $inspectionsTodayCount }}</div>
                        <div class="mt-2 text-xs text-gray-600">{{ now()->format('Y-m-d') }}</div>
                    </div>
                    <div class="px-5 py-3 bg-white/60 border-t border-emerald-100 flex items-center justify-between text-xs text-gray-600">
                        <span>Log history</span>
                        <a href="{{ route('inspections.index') }}" class="font-semibold text-emerald-700 hover:underline">View</a>
                    </div>
                </div>

                {{-- Weather tile (big, glass + contrast) --}}
                <div class="{{ $tileBase }} lg:col-span-8 bg-gradient-to-br from-slate-900 via-gray-900 to-slate-950 border-white/10">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-white/70">Weather Brief</div>
                                <div class="mt-1 text-xl font-bold text-white">{{ $station ?? 'KAVL' }}</div>

                                @php
                                    $cat = $fltCat ?? '—';
                                    $catBadge = match ($cat) {
                                        'VFR'  => 'bg-green-400/15 text-green-200 border-green-400/20',
                                        'MVFR' => 'bg-yellow-400/15 text-yellow-200 border-yellow-400/20',
                                        'IFR'  => 'bg-orange-400/15 text-orange-200 border-orange-400/20',
                                        'LIFR' => 'bg-red-400/15 text-red-200 border-red-400/20',
                                        default => 'bg-white/10 text-white/70 border-white/15',
                                    };
                                @endphp

                                <div class="mt-3 inline-flex items-center gap-2">
                                    <span class="text-xs text-white/60">Flight Category</span>
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $catBadge }}">
                                        {{ $cat }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ $liveAtcAtisUrl }}" target="_blank"
                                   class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-white/10 text-white text-sm font-semibold hover:bg-white/15 border border-white/10">
                                    ATIS (LiveATC)
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 md:grid-cols-5 gap-4">
                            @if(!$metarUi || (!$metarRaw && $wxMetarError))
                                <div class="col-span-2 md:col-span-5 rounded-2xl border border-red-400/20 bg-red-400/10 p-4 text-sm text-red-100">
                                    <div class="font-semibold">METAR unavailable</div>
                                    <div class="mt-1 text-xs opacity-80">{{ $wxMetarError ?? 'No METAR available.' }}</div>
                                </div>
                            @else
                                @php
                                    $wd = $metarUi['windDir'];
                                    $ws = $metarUi['windSpd'];
                                    $wg = $metarUi['windGst'];
                                @endphp

                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-white/60">Wind</div>
                                    <div class="mt-2 text-lg font-black text-white">
                                        {{ is_numeric($wd) ? sprintf('%03d', $wd) : '—' }}°
                                        {{ is_numeric($ws) ? $ws : '—' }}kt
                                        @if(is_numeric($wg)) <span class="text-sm text-white/60">G{{ $wg }}</span> @endif
                                    </div>
                                    <div class="mt-2 text-xs text-white/50">Dir / Spd / Gust</div>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-white/60">Visibility</div>
                                    <div class="mt-2 text-lg font-black text-white">
                                        {{ $metarUi['visib'] ?? '—' }} <span class="text-sm text-white/60 font-semibold">SM</span>
                                    </div>
                                    <div class="mt-2 text-xs text-white/50">Reported</div>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-white/60">Temp / Dew</div>
                                    <div class="mt-2 text-lg font-black text-white">
                                        {{ is_numeric($metarUi['tempC']) ? round($metarUi['tempC']) : '—' }}°C
                                        <span class="text-white/20 font-black px-1">/</span>
                                        {{ is_numeric($metarUi['dewpC']) ? round($metarUi['dewpC']) : '—' }}°C
                                    </div>
                                    <div class="mt-2 text-xs text-white/50">Celsius</div>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-white/60">Altimeter</div>
                                    <div class="mt-2 text-lg font-black text-white">
                                        @if(is_numeric($metarUi['altimInHg']))
                                            {{ number_format($metarUi['altimInHg'], 2) }}" <span class="text-sm text-white/60 font-semibold">Hg</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-white/50">
                                        @if(is_numeric($metarUi['altimHpa']))
                                            {{ number_format($metarUi['altimHpa'], 1) }} hPa
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>

                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-xs font-semibold uppercase tracking-wide text-white/60">Ceiling / Sky</div>
                                    <div class="mt-2 text-lg font-black text-white">
                                        @if(is_numeric($metarUi['ceilingFt']))
                                            {{ number_format($metarUi['ceilingFt']) }} <span class="text-sm text-white/60 font-semibold">ft</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                    <div class="mt-2 text-xs text-white/50">{{ $metarUi['cover'] ?? '—' }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 flex items-center justify-between text-xs text-white/50">
                            <span>{{ $metarTime?->format('Y-m-d H:i') ?? '—' }}Z</span>
                            <details class="group">
                                <summary class="cursor-pointer select-none hover:text-white/70">Raw METAR/TAF</summary>
                                <div class="mt-3 rounded-2xl border border-white/10 bg-white/5 p-4 text-white/80 font-mono whitespace-pre-wrap">
                                    {{ $metarRaw ?? ($wxMetarError ?? 'No METAR available.') }}

                                    {{ "\n\n" }}

                                    {{ $tafRaw ?? ($wxTafError ?? 'No TAF available.') }}
                                </div>
                            </details>
                        </div>
                    </div>
                </div>

                {{-- ATIS tile (small but bold) --}}
                <div class="{{ $tileBase }} lg:col-span-4 bg-gradient-to-br from-white via-slate-50 to-slate-100 border-gray-200">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">ATIS</div>
                                <div class="mt-1 text-lg font-bold text-gray-900">{{ $station ?? 'KAVL' }} ATIS</div>
                                <div class="mt-2 text-xs text-gray-600">LiveATC link (no D-ATIS for AVL).</div>
                            </div>
                            <div class="h-10 w-10 rounded-2xl bg-gray-900 text-white flex items-center justify-center font-black">
                                A
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl border border-gray-200 bg-white p-4">
                            <div class="text-xs text-gray-600">Frequency</div>
                            <div class="mt-1 text-2xl font-black text-gray-900">120.200</div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-3">
                            <a href="{{ $liveAtcAtisUrl }}" target="_blank"
                               class="w-full inline-flex justify-center px-3 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                                Listen on LiveATC
                            </a>
                            <a href="{{ $liveAtcAllFeedsUrl }}" target="_blank"
                               class="w-full inline-flex justify-center px-3 py-2.5 rounded-xl border border-gray-300 bg-white text-sm text-gray-800 hover:bg-gray-50 shadow-sm">
                                View all feeds
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Today Inspections (bento tile) --}}
                <div class="col-span-1 lg:col-span-6 w-full min-w-0">
                    <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-slate-50 shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="px-6 py-4 border-b border-emerald-100 flex items-center justify-between bg-white/60 backdrop-blur">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-2xl bg-emerald-600/10 border border-emerald-200 flex items-center justify-center">
                                    <span class="text-emerald-800 font-black">I</span>
                                </div>

                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Today</div>
                                    <div class="mt-0.5 text-lg font-bold text-gray-900">Inspections</div>
                                    <div class="text-xs text-gray-600 mt-0.5">Latest submitted today</div>
                                </div>
                            </div>

                            <a href="{{ route('inspections.index') }}" class="text-sm font-semibold text-emerald-800 hover:underline">
                                View all
                            </a>
                        </div>

                        <div class="p-6">
                            @if($todayInspections->isEmpty())
                                <div class="rounded-2xl border border-emerald-100 bg-white/70 p-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900">None logged yet</div>
                                    <div class="text-xs text-gray-600 mt-1">Create one to keep Part 139 cadence clean.</div>

                                    <div class="mt-4">
                                        <a href="{{ route('inspections.create') }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-900 text-white text-sm font-semibold hover:bg-emerald-950 shadow-sm">
                                            <span class="text-base leading-none">+</span> Create Inspection
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($todayInspections as $insp)
                                        @php
                                            $header = is_array($insp->header ?? null) ? $insp->header : [];
                                            $overall = $header['overall_status'] ?? '—';

                                            $overallPill = match ($overall) {
                                                'Satisfactory'   => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'Unsatisfactory' => 'bg-red-100 text-red-800 border-red-200',
                                                default          => 'bg-gray-100 text-gray-800 border-gray-200',
                                            };

                                            $am = $header['am_time'] ?? null;
                                            $pm = $header['pm_time'] ?? null;

                                            $fmtTime = function($t) {
                                                try { return $t ? \Illuminate\Support\Carbon::parse($t)->format('H:i') : null; }
                                                catch (\Throwable $e) { return null; }
                                            };
                                            $amT = $fmtTime($am);
                                            $pmT = $fmtTime($pm);
                                        @endphp

                                        <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur p-4 hover:shadow-md transition min-w-0">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a href="{{ route('inspections.show', $insp) }}"
                                                       class="text-base font-bold text-emerald-900 hover:underline">
                                                        {{ $insp->insp_number }}
                                                    </a>

                                                    <div class="mt-1 text-xs text-gray-600">
                                                        Inspector:
                                                        <span class="font-semibold text-gray-900">{{ $insp->inspector?->name ?? '—' }}</span>
                                                    </div>

                                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $overallPill }}">
                                                            {{ $overall }}
                                                        </span>

                                                        <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-2 py-0.5">
                                                            Logged: {{ $insp->created_at?->format('H:i') ?? '—' }}
                                                        </span>

                                                        @if($amT)
                                                            <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-2 py-0.5">
                                                                AM: <span class="font-semibold text-gray-900 ml-1">{{ $amT }}</span>
                                                            </span>
                                                        @endif

                                                        @if($pmT)
                                                            <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-2 py-0.5">
                                                                PM: <span class="font-semibold text-gray-900 ml-1">{{ $pmT }}</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex flex-col items-end gap-2 shrink-0">
                                                    <a href="{{ route('inspections.print', $insp) }}" target="_blank"
                                                       class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-50">
                                                        Print
                                                    </a>
                                                    <a href="{{ route('inspections.pdf', $insp) }}"
                                                       class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-50">
                                                        PDF
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Open Work Orders (bento tile) --}}
                <div class="col-span-1 lg:col-span-6 w-full min-w-0">
                    <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 via-white to-slate-50 shadow-sm overflow-hidden hover:shadow-md transition">
                        <div class="px-6 py-4 border-b border-blue-100 flex items-center justify-between bg-white/60 backdrop-blur">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-2xl bg-blue-600/10 border border-blue-200 flex items-center justify-center">
                                    <span class="text-blue-800 font-black">W</span>
                                </div>

                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Queue</div>
                                    <div class="mt-0.5 text-lg font-bold text-gray-900">Open Work Orders</div>
                                    <div class="text-xs text-gray-600 mt-0.5">Top priority / most recent</div>
                                </div>
                            </div>

                            <a href="{{ route('work-orders.index') }}" class="text-sm font-semibold text-blue-800 hover:underline">
                                View all
                            </a>
                        </div>

                        <div class="p-6">
                            @if($recentOpenWorkOrders->isEmpty())
                                <div class="rounded-2xl border border-blue-100 bg-white/70 p-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900">No open work orders</div>
                                    <div class="text-xs text-gray-600 mt-1">Either you’re on top of it… or nobody is logging. Both are suspicious.</div>

                                    <div class="mt-4">
                                        <a href="{{ route('work-orders.create') }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                                            <span class="text-base leading-none">+</span> Create Work Order
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($recentOpenWorkOrders as $wo)
                                        @php
                                            $pri = $wo->priority ?? '—';
                                            $priPill = match ($pri) {
                                                'Critical' => 'bg-red-100 text-red-800 border-red-200',
                                                'High'     => 'bg-orange-100 text-orange-800 border-orange-200',
                                                'Medium'   => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'Low'      => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                default    => 'bg-gray-100 text-gray-800 border-gray-200',
                                            };

                                            $due = $wo->due_date ? \Illuminate\Support\Carbon::parse($wo->due_date) : null;
                                            $dueLabel = $due ? $due->format('Y-m-d') : '—';
                                            $isOverdue = $due ? $due->isPast() : false;
                                        @endphp

                                        <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur p-4 hover:shadow-md transition min-w-0">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a href="{{ route('work-orders.show', $wo) }}"
                                                       class="text-base font-bold text-blue-900 hover:underline">
                                                        {{ $wo->wo_number ?? ('WO-'.$wo->id) }}
                                                    </a>

                                                    <div class="mt-1 text-sm font-semibold text-gray-900 truncate">
                                                        {{ $wo->title }}
                                                    </div>

                                                    <div class="mt-1 text-xs text-gray-600 truncate">
                                                        Location:
                                                        <span class="font-semibold text-gray-900">{{ $wo->location ?? '—' }}</span>
                                                    </div>

                                                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $priPill }}">
                                                            {{ $pri }}
                                                        </span>

                                                        <span class="inline-flex items-center rounded-full border border-gray-200 bg-gray-50 px-2 py-0.5">
                                                            Due: <span class="font-semibold text-gray-900 ml-1">{{ $dueLabel }}</span>
                                                        </span>

                                                        @if($isOverdue)
                                                            <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 text-red-800 px-2 py-0.5 font-semibold">
                                                                Overdue
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex flex-col items-end gap-2 shrink-0">
                                                    <a href="{{ route('work-orders.show', $wo) }}"
                                                       class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-gray-50">
                                                        View
                                                    </a>
                                                    <a href="{{ route('work-orders.edit', $wo) }}"
                                                       class="inline-flex items-center justify-center rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                        Edit
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Recent Inspections (table tile) --}}
                <div class="col-span-1 lg:col-span-12 w-full min-w-0">
                    <div class="rounded-2xl border border-gray-200 bg-white/80 backdrop-blur shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gradient-to-r from-white via-slate-50 to-slate-100">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">History</div>
                                <div class="mt-1 font-bold text-gray-900">Recent Inspections</div>
                                <div class="text-xs text-gray-600 mt-1">Last 8 logged</div>
                            </div>
                            <a href="{{ route('inspections.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">
                                Go to inspections
                            </a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 text-xs uppercase tracking-wide text-gray-600">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold">INSP #</th>
                                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                                        <th class="px-4 py-3 text-left font-semibold">Overall</th>
                                        <th class="px-4 py-3 text-left font-semibold">Inspector</th>
                                        <th class="px-4 py-3 text-right font-semibold">Actions</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse($recentInspections as $insp)
                                        @php
                                            $header = is_array($insp->header ?? null) ? $insp->header : [];
                                            $overall = $header['overall_status'] ?? '—';

                                            $overallPill = match ($overall) {
                                                'Satisfactory'   => 'bg-green-100 text-green-800 border-green-200',
                                                'Unsatisfactory' => 'bg-red-100 text-red-800 border-red-200',
                                                default          => 'bg-gray-100 text-gray-800 border-gray-200',
                                            };
                                        @endphp

                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-3 font-semibold text-blue-700">
                                                <a href="{{ route('inspections.show', $insp) }}" class="hover:underline">
                                                    {{ $insp->insp_number }}
                                                </a>
                                            </td>

                                            <td class="px-4 py-3 text-gray-900">
                                                {{ $insp->inspection_date?->format('Y-m-d') ?? '—' }}
                                            </td>

                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-semibold {{ $overallPill }}">
                                                    {{ $overall }}
                                                </span>
                                            </td>

                                            <td class="px-4 py-3 text-gray-900">
                                                {{ $insp->inspector?->name ?? '—' }}
                                            </td>

                                            <td class="px-4 py-3 text-right">
                                                <a href="{{ route('inspections.print', $insp) }}" target="_blank" class="text-gray-700 hover:underline mr-3">
                                                    Print
                                                </a>
                                                <a href="{{ route('inspections.pdf', $insp) }}" class="text-gray-700 hover:underline">
                                                    PDF
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                                No inspections found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


</div>



        </div>
    </div>
</x-sidebar-app-layout>
