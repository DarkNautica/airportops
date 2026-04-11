{{-- resources/views/inspections/index.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Part 139 Inspections</h1>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('inspections.create') }}"
           class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
            <span class="text-base leading-none">+</span> New Inspection
        </a>
    </x-slot>

    @php
        $totalOnPage = $inspections->count();
        $todayCount = $inspections->filter(fn($i) => optional($i->inspection_date)->isToday())->count();
        $openCount = $inspections->filter(fn($i) => ($i->status ?? 'Open') === 'Open')->count();

        $getHeader = function($inspection) {
            return is_array($inspection->header ?? null) ? $inspection->header : [];
        };

        $fmtTime = function($t) {
            try { return $t ? \Illuminate\Support\Carbon::parse($t)->format('H:i') : '—'; }
            catch (\Throwable $e) { return '—'; }
        };

        $fmtDate = fn($d) => $d ? $d->format('Y-m-d') : '—';
    @endphp

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            {{-- Flash --}}
            @if (session('success'))
                <div class="bg-white rounded-xl shadow-card overflow-hidden border-l-4 border-l-emerald-400">
                    <div class="px-5 py-4 flex items-start gap-3">
                        <x-status-dot color="green" :pulse="true" size="md" class="mt-1" />
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Success</div>
                            <div class="text-sm text-gray-600 mt-0.5">{{ session('success') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- KPI strip --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] bg-[#2563EB]"></div>
                    <div class="p-5">
                        <div class="font-serif text-3xl text-gray-900">{{ $totalOnPage }}</div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">Inspections (This Page)</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] bg-[#16A34A]"></div>
                    <div class="p-5">
                        <div class="font-serif text-3xl text-gray-900">{{ $todayCount }}</div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">Today {{ now()->format('Y-m-d') }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] bg-[#D97706]"></div>
                    <div class="p-5">
                        <div class="font-serif text-3xl text-gray-900">{{ $openCount }}</div>
                        <div class="mt-2 font-mono text-[10px] uppercase tracking-widest text-slate-500">Open (Status = Open)</div>
                    </div>
                </div>
            </div>

            {{-- Filters card --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">Search & Filter</span>
                    @if(request()->filled('q') || request()->filled('date_from') || request()->filled('date_to'))
                        <a href="{{ route('inspections.index') }}"
                           class="text-sm font-semibold text-brand hover:text-brand-hover">
                            Reset
                        </a>
                    @endif
                </div>

                <div class="px-5 py-4">
                    <form method="GET" action="{{ route('inspections.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text"
                                       name="q"
                                       value="{{ request('q') }}"
                                       placeholder="INSP-000123, Daily Inspection..."
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date from</label>
                                <input type="date"
                                       name="date_from"
                                       value="{{ request('date_from') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date to</label>
                                <input type="date"
                                       name="date_to"
                                       value="{{ request('date_to') }}"
                                       class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                            </div>

                            <div class="md:col-span-1 flex gap-2">
                                <button type="submit"
                                        class="w-full inline-flex justify-center rounded-lg bg-gray-900 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-black transition">
                                    Apply
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 text-xs text-slate-400">
                            Filters persist through pagination automatically.
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">Inspection Records</span>
                    <span class="font-mono text-[10px] text-slate-500">{{ $inspections->total() ?? $inspections->count() }} total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="c139-table w-full">
                        <thead>
                            <tr>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">INSP #</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Date</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Overall</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Times</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Status</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Inspector</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-right">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-surface-border">
                            @forelse ($inspections as $inspection)
                                @php
                                    $header = $getHeader($inspection);

                                    $overall = $header['overall_status'] ?? null;
                                    $overallVariant = match ($overall) {
                                        'Satisfactory' => 'satisfactory',
                                        'Unsatisfactory' => 'unsatisfactory',
                                        default => 'neutral',
                                    };

                                    $status = $inspection->status ?? 'Open';
                                    $statusVariant = match ($status) {
                                        'Certified' => 'certified',
                                        'Completed' => 'certified',
                                        'Issues Noted' => 'warning',
                                        'Work Orders Created' => 'progress',
                                        'Closed' => 'closed',
                                        default => 'open',
                                    };

                                    $crash = $fmtTime($header['crash_phone_test_time'] ?? null);
                                    $am = $fmtTime($header['am_time'] ?? null);
                                    $pm = $fmtTime($header['pm_time'] ?? null);
                                    $other = $fmtTime($header['other_time'] ?? null);

                                    $type = $inspection->inspection_type ?? 'FAA Part 139 - Daily Inspection';

                                    $locked = (bool)($inspection->is_locked ?? false);
                                @endphp

                                <tr class="h-11 hover:bg-[#F8FAFC] transition-colors">
                                    {{-- INSP --}}
                                    <td class="px-4 py-2.5 text-sm">
                                        <div class="flex flex-col gap-1.5">
                                            <a href="{{ route('inspections.show', $inspection) }}"
                                               class="font-mono font-semibold text-brand hover:text-brand-hover">
                                                {{ $inspection->insp_number }}
                                            </a>
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-xs text-slate-500">{{ $type }}</span>
                                                @if($locked)
                                                    <x-badge variant="locked">Locked</x-badge>
                                                @else
                                                    <x-badge variant="open">Editable</x-badge>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-4 py-2.5 text-sm text-gray-900 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $fmtDate($inspection->inspection_date) }}</span>
                                            <span class="text-xs text-slate-500">{{ $header['inspection_day'] ?? '—' }}</span>
                                        </div>
                                    </td>

                                    {{-- Overall --}}
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <x-badge :variant="$overallVariant">{{ $overall ?? '—' }}</x-badge>
                                    </td>

                                    {{-- Times --}}
                                    <td class="px-4 py-2.5">
                                        <div class="font-mono text-[11px] text-gray-700 grid grid-cols-2 gap-x-6 gap-y-1">
                                            <div><span class="text-slate-500">Crash:</span> {{ $crash }}</div>
                                            <div><span class="text-slate-500">Other:</span> {{ $other }}</div>
                                            <div><span class="text-slate-500">AM:</span> {{ $am }}</div>
                                            <div><span class="text-slate-500">PM:</span> {{ $pm }}</div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-2.5 whitespace-nowrap">
                                        <x-badge :variant="$statusVariant">{{ $status }}</x-badge>
                                    </td>

                                    {{-- Inspector --}}
                                    <td class="px-4 py-2.5 text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $inspection->inspector?->name ?? '—' }}</span>
                                            <span class="font-mono text-[11px] text-slate-500">
                                                {{ $inspection->created_at?->format('Y-m-d H:i') ?? '—' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-4 py-2.5 text-right text-sm whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2 flex-wrap justify-end">
                                            <a href="{{ route('inspections.show', $inspection) }}"
                                               class="inline-flex items-center rounded-lg border border-surface-border px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-[#F8FAFC] transition">
                                                View
                                            </a>

                                            <a href="{{ route('inspections.print', $inspection) }}"
                                               target="_blank"
                                               class="inline-flex items-center rounded-lg border border-surface-border px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-[#F8FAFC] transition">
                                                Print
                                            </a>

                                            <a href="{{ route('inspections.pdf', $inspection) }}"
                                               class="inline-flex items-center rounded-lg border border-surface-border px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-[#F8FAFC] transition">
                                                PDF
                                            </a>

                                            @can('update', $inspection)
                                                @if(!$locked)
                                                    <a href="{{ route('inspections.edit', $inspection) }}"
                                                       class="inline-flex items-center rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 transition">
                                                        Edit
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-semibold text-slate-500">
                                                        Locked
                                                    </span>
                                                @endif
                                            @endcan

                                            @can('delete', $inspection)
                                                <form action="{{ route('inspections.destroy', $inspection) }}"
                                                      method="POST"
                                                      class="inline"
                                                      onsubmit="return confirm('Delete this inspection record?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center rounded-lg border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100 transition">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-16 text-center">
                                        <div class="text-slate-400">
                                            <div class="text-sm">No inspections logged yet.</div>
                                            <a href="{{ route('inspections.create') }}"
                                               class="mt-2 inline-flex text-sm font-semibold text-brand hover:text-brand-hover">
                                                + Create your first inspection
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-surface-border">
                    {{ $inspections->links() }}
                </div>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
