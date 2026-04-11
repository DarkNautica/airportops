<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Dashboard</h1>
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

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            {{-- Stat Cards Row --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">

                {{-- Card 1: Open Work Orders --}}
                <a href="{{ route('work-orders.index') }}" class="block bg-white rounded-xl shadow-card border border-surface-border overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#2563EB]"></div>
                    <div class="p-5">
                        <div class="font-serif text-3xl text-gray-900">{{ $openWorkOrdersCount }}</div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">Open Work Orders</div>
                    </div>
                </a>

                {{-- Card 2: Critical --}}
                <a href="{{ route('work-orders.index') }}" class="block bg-white rounded-xl shadow-card border border-surface-border overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#DC2626]"></div>
                    <div class="p-5">
                        <div class="font-serif text-3xl text-gray-900">{{ $criticalOpenCount }}</div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">Critical</div>
                    </div>
                </a>

                {{-- Card 3: Overdue --}}
                <a href="{{ route('work-orders.index') }}" class="block bg-white rounded-xl shadow-card border border-surface-border overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#D97706]"></div>
                    <div class="p-5">
                        <div class="font-serif text-3xl text-gray-900">{{ $overdueCount }}</div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">Overdue</div>
                    </div>
                </a>

                {{-- Card 4: Inspections Today --}}
                <a href="{{ route('inspections.index') }}" class="block bg-white rounded-xl shadow-card border border-surface-border overflow-hidden hover:shadow-md transition">
                    <div class="h-[2px] bg-[#16A34A]"></div>
                    <div class="p-5">
                        <div class="font-serif text-3xl text-gray-900">{{ $inspectionsTodayCount }}</div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">Inspections Today</div>
                    </div>
                </a>

                {{-- Card 5: System Status --}}
                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] bg-[#16A34A]"></div>
                    <div class="p-5">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                            </span>
                            <span class="font-serif text-lg text-gray-900">Online</span>
                        </div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">System Status</div>
                    </div>
                </div>
            </div>

            {{-- Alert Banner --}}
            @if ($missingInspectionToday || $overdueCount > 0)
                <div class="bg-white rounded-xl shadow-card overflow-hidden border-l-4 border-l-amber-400">
                    <div class="p-5">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex-shrink-0">
                                <div class="h-2.5 w-2.5 rounded-full bg-amber-400"></div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div class="font-semibold text-gray-900 text-sm">Operational Alerts</div>
                                    <div class="font-mono text-[10px] uppercase tracking-widest text-slate-500">Action Required</div>
                                </div>

                                <ul class="list-disc pl-5 text-sm mt-2 space-y-1 text-gray-700">
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
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-semibold hover:bg-amber-600">
                                            Create Inspection
                                        </a>
                                    @endif
                                    @if($overdueCount > 0)
                                        <a href="{{ route('work-orders.index') }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg border border-amber-300 bg-white text-amber-800 text-xs font-semibold hover:bg-amber-50">
                                            Review Work Orders
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Weather + ATIS Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Weather Tile --}}
                <div class="lg:col-span-8 bg-[#0E1520] rounded-xl shadow-card overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="font-mono text-[10px] uppercase tracking-widest text-white/50">Weather Brief</div>
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
                                    <span class="font-mono text-[10px] uppercase tracking-widest text-white/50">Flight Category</span>
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $catBadge }}">
                                        {{ $cat }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ $liveAtcAtisUrl }}" target="_blank"
                                   class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-white/10 text-white text-sm font-semibold hover:bg-white/15 border border-white/10">
                                    ATIS (LiveATC)
                                </a>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 md:grid-cols-5 gap-4">
                            @if(!$metarUi || (!$metarRaw && $wxMetarError))
                                <div class="col-span-2 md:col-span-5 rounded-lg border border-red-400/20 bg-red-400/10 p-4 text-sm text-red-100">
                                    <div class="font-semibold">METAR unavailable</div>
                                    <div class="mt-1 text-xs opacity-80">{{ $wxMetarError ?? 'No METAR available.' }}</div>
                                </div>
                            @else
                                @php
                                    $wd = $metarUi['windDir'];
                                    $ws = $metarUi['windSpd'];
                                    $wg = $metarUi['windGst'];
                                @endphp

                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <div class="font-mono text-[10px] uppercase tracking-widest text-white/50">Wind</div>
                                    <div class="mt-2 text-lg font-bold text-white">
                                        {{ is_numeric($wd) ? sprintf('%03d', $wd) : '—' }}°
                                        {{ is_numeric($ws) ? $ws : '—' }}kt
                                        @if(is_numeric($wg)) <span class="text-sm text-white/60">G{{ $wg }}</span> @endif
                                    </div>
                                    <div class="mt-2 font-mono text-[10px] text-white/40">Dir / Spd / Gust</div>
                                </div>

                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <div class="font-mono text-[10px] uppercase tracking-widest text-white/50">Visibility</div>
                                    <div class="mt-2 text-lg font-bold text-white">
                                        {{ $metarUi['visib'] ?? '—' }} <span class="text-sm text-white/60 font-semibold">SM</span>
                                    </div>
                                    <div class="mt-2 font-mono text-[10px] text-white/40">Reported</div>
                                </div>

                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <div class="font-mono text-[10px] uppercase tracking-widest text-white/50">Temp / Dew</div>
                                    <div class="mt-2 text-lg font-bold text-white">
                                        {{ is_numeric($metarUi['tempC']) ? round($metarUi['tempC']) : '—' }}°C
                                        <span class="text-white/20 font-bold px-1">/</span>
                                        {{ is_numeric($metarUi['dewpC']) ? round($metarUi['dewpC']) : '—' }}°C
                                    </div>
                                    <div class="mt-2 font-mono text-[10px] text-white/40">Celsius</div>
                                </div>

                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <div class="font-mono text-[10px] uppercase tracking-widest text-white/50">Altimeter</div>
                                    <div class="mt-2 text-lg font-bold text-white">
                                        @if(is_numeric($metarUi['altimInHg']))
                                            {{ number_format($metarUi['altimInHg'], 2) }}" <span class="text-sm text-white/60 font-semibold">Hg</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                    <div class="mt-2 font-mono text-[10px] text-white/40">
                                        @if(is_numeric($metarUi['altimHpa']))
                                            {{ number_format($metarUi['altimHpa'], 1) }} hPa
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>

                                <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                                    <div class="font-mono text-[10px] uppercase tracking-widest text-white/50">Ceiling / Sky</div>
                                    <div class="mt-2 text-lg font-bold text-white">
                                        @if(is_numeric($metarUi['ceilingFt']))
                                            {{ number_format($metarUi['ceilingFt']) }} <span class="text-sm text-white/60 font-semibold">ft</span>
                                        @else
                                            —
                                        @endif
                                    </div>
                                    <div class="mt-2 font-mono text-[10px] text-white/40">{{ $metarUi['cover'] ?? '—' }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="mt-5 flex items-center justify-between text-xs text-white/50">
                            <span>{{ $metarTime?->format('Y-m-d H:i') ?? '—' }}Z</span>
                            <details class="group">
                                <summary class="cursor-pointer select-none hover:text-white/70 font-mono text-[10px] uppercase tracking-widest">Raw METAR/TAF</summary>
                                <div class="mt-3 rounded-lg border border-white/10 bg-white/5 p-4 text-white/80 font-mono text-xs whitespace-pre-wrap">
                                    {{ $metarRaw ?? ($wxMetarError ?? 'No METAR available.') }}

                                    {{ "\n\n" }}

                                    {{ $tafRaw ?? ($wxTafError ?? 'No TAF available.') }}
                                </div>
                            </details>
                        </div>
                    </div>
                </div>

                {{-- ATIS Tile --}}
                <div class="lg:col-span-4 bg-white rounded-xl shadow-card overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <div class="font-mono text-[10px] uppercase tracking-widest text-slate-500">ATIS</div>
                                <div class="mt-1 text-lg font-bold text-gray-900">{{ $station ?? 'KAVL' }} ATIS</div>
                                <div class="mt-2 text-xs text-slate-500">LiveATC link (no D-ATIS for AVL).</div>
                            </div>
                            <div class="h-10 w-10 rounded-xl bg-gray-900 text-white flex items-center justify-center font-bold text-sm">
                                A
                            </div>
                        </div>

                        <div class="mt-5 rounded-lg border border-surface-border bg-surface p-4">
                            <div class="font-mono text-[10px] uppercase tracking-widest text-slate-500">Frequency</div>
                            <div class="mt-1 text-2xl font-bold text-gray-900 font-mono">120.200</div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 gap-3">
                            <a href="{{ $liveAtcAtisUrl }}" target="_blank"
                               class="w-full inline-flex justify-center px-3 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                                Listen on LiveATC
                            </a>
                            <a href="{{ $liveAtcAllFeedsUrl }}" target="_blank"
                               class="w-full inline-flex justify-center px-3 py-2.5 rounded-lg border border-gray-300 bg-white text-sm text-gray-800 hover:bg-gray-50 shadow-sm">
                                View all feeds
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Today's Inspections + Open Work Orders Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                {{-- Today's Inspections --}}
                <div class="lg:col-span-6">
                    <div class="c139-card bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-border flex items-center justify-between">
                            <div class="font-mono text-[10px] uppercase tracking-widest text-slate-500">Today</div>
                            <a href="{{ route('inspections.index') }}" class="text-xs font-semibold text-brand hover:underline">
                                View all
                            </a>
                        </div>

                        <div class="p-6">
                            @if($todayInspections->isEmpty())
                                <div class="rounded-lg border border-surface-border bg-surface p-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900">None logged yet</div>
                                    <div class="text-xs text-slate-500 mt-1">Create one to keep Part 139 cadence clean.</div>

                                    <div class="mt-4">
                                        <a href="{{ route('inspections.create') }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                                            <span class="text-base leading-none">+</span> Create Inspection
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3">
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

                                        <div class="rounded-lg border border-surface-border bg-white p-4 hover:shadow-sm transition">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a href="{{ route('inspections.show', $insp) }}"
                                                       class="text-sm font-bold text-gray-900 hover:text-brand hover:underline">
                                                        {{ $insp->insp_number }}
                                                    </a>

                                                    <div class="mt-1 text-xs text-slate-500">
                                                        Inspector:
                                                        <span class="font-semibold text-gray-900">{{ $insp->inspector?->name ?? '—' }}</span>
                                                    </div>

                                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-xs">
                                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $overallPill }}">
                                                            {{ $overall }}
                                                        </span>

                                                        <span class="inline-flex items-center rounded-full border border-surface-border bg-surface px-2 py-0.5 text-[10px] text-slate-500">
                                                            Logged: {{ $insp->created_at?->format('H:i') ?? '—' }}
                                                        </span>

                                                        @if($amT)
                                                            <span class="inline-flex items-center rounded-full border border-surface-border bg-surface px-2 py-0.5 text-[10px] text-slate-500">
                                                                AM: <span class="font-semibold text-gray-900 ml-1">{{ $amT }}</span>
                                                            </span>
                                                        @endif

                                                        @if($pmT)
                                                            <span class="inline-flex items-center rounded-full border border-surface-border bg-surface px-2 py-0.5 text-[10px] text-slate-500">
                                                                PM: <span class="font-semibold text-gray-900 ml-1">{{ $pmT }}</span>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex flex-col items-end gap-2 shrink-0">
                                                    <a href="{{ route('inspections.print', $insp) }}" target="_blank"
                                                       class="inline-flex items-center justify-center rounded-lg border border-surface-border bg-white px-3 py-1.5 text-[10px] font-semibold text-gray-700 hover:bg-surface">
                                                        Print
                                                    </a>
                                                    <a href="{{ route('inspections.pdf', $insp) }}"
                                                       class="inline-flex items-center justify-center rounded-lg border border-surface-border bg-white px-3 py-1.5 text-[10px] font-semibold text-gray-700 hover:bg-surface">
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

                {{-- Open Work Orders --}}
                <div class="lg:col-span-6">
                    <div class="c139-card bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="px-6 py-4 border-b border-surface-border flex items-center justify-between">
                            <div class="font-mono text-[10px] uppercase tracking-widest text-slate-500">Queue</div>
                            <a href="{{ route('work-orders.index') }}" class="text-xs font-semibold text-brand hover:underline">
                                View all
                            </a>
                        </div>

                        <div class="p-6">
                            @if($recentOpenWorkOrders->isEmpty())
                                <div class="rounded-lg border border-surface-border bg-surface p-4 text-sm text-gray-700">
                                    <div class="font-semibold text-gray-900">No open work orders</div>
                                    <div class="text-xs text-slate-500 mt-1">Either you're on top of it... or nobody is logging.</div>

                                    <div class="mt-4">
                                        <a href="{{ route('work-orders.create') }}"
                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                                            <span class="text-base leading-none">+</span> Create Work Order
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3">
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

                                        <div class="rounded-lg border border-surface-border bg-white p-4 hover:shadow-sm transition">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="min-w-0">
                                                    <a href="{{ route('work-orders.show', $wo) }}"
                                                       class="text-sm font-bold text-gray-900 hover:text-brand hover:underline">
                                                        {{ $wo->wo_number ?? ('WO-'.$wo->id) }}
                                                    </a>

                                                    <div class="mt-1 text-sm font-semibold text-gray-900 truncate">
                                                        {{ $wo->title }}
                                                    </div>

                                                    <div class="mt-1 text-xs text-slate-500 truncate">
                                                        Location:
                                                        <span class="font-semibold text-gray-900">{{ $wo->location ?? '—' }}</span>
                                                    </div>

                                                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $priPill }}">
                                                            {{ $pri }}
                                                        </span>

                                                        <span class="inline-flex items-center rounded-full border border-surface-border bg-surface px-2 py-0.5 text-[10px] text-slate-500">
                                                            Due: <span class="font-semibold text-gray-900 ml-1">{{ $dueLabel }}</span>
                                                        </span>

                                                        @if($isOverdue)
                                                            <span class="inline-flex items-center rounded-full border border-red-200 bg-red-50 text-red-800 px-2 py-0.5 text-[10px] font-semibold">
                                                                Overdue
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="flex flex-col items-end gap-2 shrink-0">
                                                    <a href="{{ route('work-orders.show', $wo) }}"
                                                       class="inline-flex items-center justify-center rounded-lg border border-surface-border bg-white px-3 py-1.5 text-[10px] font-semibold text-gray-700 hover:bg-surface">
                                                        View
                                                    </a>
                                                    <a href="{{ route('work-orders.edit', $wo) }}"
                                                       class="inline-flex items-center justify-center rounded-lg border border-brand/20 bg-brand/5 px-3 py-1.5 text-[10px] font-semibold text-brand hover:bg-brand/10">
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
            </div>

            {{-- Recent Inspections Table --}}
            <div class="c139-card bg-white rounded-xl shadow-card overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-border flex items-center justify-between">
                    <div class="font-mono text-[10px] uppercase tracking-widest text-slate-500">History</div>
                    <a href="{{ route('inspections.index') }}" class="text-xs font-semibold text-brand hover:underline">
                        Go to inspections
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="c139-table min-w-full text-sm">
                        <thead class="bg-surface">
                            <tr>
                                <th class="px-4 py-3 text-left font-mono text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Insp #</th>
                                <th class="px-4 py-3 text-left font-mono text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Date</th>
                                <th class="px-4 py-3 text-left font-mono text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Overall</th>
                                <th class="px-4 py-3 text-left font-mono text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Inspector</th>
                                <th class="px-4 py-3 text-right font-mono text-[10px] uppercase tracking-widest text-slate-500 font-semibold">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-surface-border bg-white">
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

                                <tr class="hover:bg-surface/50">
                                    <td class="px-4 py-3 font-semibold text-brand">
                                        <a href="{{ route('inspections.show', $insp) }}" class="hover:underline">
                                            {{ $insp->insp_number }}
                                        </a>
                                    </td>

                                    <td class="px-4 py-3 text-gray-900">
                                        {{ $insp->inspection_date?->format('Y-m-d') ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[10px] font-semibold {{ $overallPill }}">
                                            {{ $overall }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-gray-900">
                                        {{ $insp->inspector?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('inspections.print', $insp) }}" target="_blank" class="text-slate-500 hover:text-gray-900 hover:underline mr-3 text-xs">
                                            Print
                                        </a>
                                        <a href="{{ route('inspections.pdf', $insp) }}" class="text-slate-500 hover:text-gray-900 hover:underline text-xs">
                                            PDF
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-slate-500 text-sm">
                                        No inspections found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- Bottom Status Bar --}}
        <div class="h-8 bg-brand flex items-center justify-between px-6">
            <span class="font-mono text-[11px] text-white">CLEAR139 v1.0 - Asheville Regional Airport (KAVL)</span>
            <span id="zulu-clock" class="font-mono text-[11px] text-white">--:--:--Z</span>
        </div>
    </div>

    @push('scripts')
    <script>
        (function() {
            function updateZulu() {
                var now = new Date();
                var h = String(now.getUTCHours()).padStart(2, '0');
                var m = String(now.getUTCMinutes()).padStart(2, '0');
                var s = String(now.getUTCSeconds()).padStart(2, '0');
                document.getElementById('zulu-clock').textContent = h + ':' + m + ':' + s + 'Z';
            }
            updateZulu();
            setInterval(updateZulu, 1000);
        })();
    </script>
    @endpush
</x-sidebar-app-layout>
