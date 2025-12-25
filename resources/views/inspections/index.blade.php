{{-- resources/views/inspections/index.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">Part 139 Inspections</h2>
                <p class="text-sm text-gray-600 mt-1">Daily FAA Part 139 safety self-inspections (print/PDF ready).</p>
            </div>

            <a href="{{ route('inspections.create') }}"
               class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-black">
                + New Inspection
            </a>
        </div>
    </x-slot>

    @php
        // Lightweight stats from the current page set (fast; no controller changes)
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

        $badge = function($text, $type = 'gray') {
            $map = [
                'green' => 'bg-green-50 text-green-700 ring-green-600/20',
                'yellow' => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
                'red' => 'bg-red-50 text-red-700 ring-red-600/20',
                'blue' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                'indigo' => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
                'gray' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
            ];
            $cls = $map[$type] ?? $map['gray'];

            return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset '.$cls.'">'.$text.'</span>';
        };
    @endphp

    <div class="py-10 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash --}}
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-900 shadow-sm">
                    <div class="font-semibold">Success</div>
                    <div class="text-sm mt-1">{{ session('success') }}</div>
                </div>
            @endif

            {{-- KPI strip --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500">Inspections (this page)</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $totalOnPage }}</div>
                    <div class="mt-3 text-xs text-gray-500">Visible in this results page</div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500">Today (this page)</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $todayCount }}</div>
                    <div class="mt-3 text-xs text-gray-500">{{ now()->format('Y-m-d') }}</div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500">Open (this page)</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $openCount }}</div>
                    <div class="mt-3 text-xs text-gray-500">Status = Open</div>
                </div>
            </div>

            {{-- Filters card --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-4 sm:px-6 lg:px-8 py-6 border-b border-gray-200">
                    <div class="sm:flex sm:items-center sm:justify-between gap-4">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold text-gray-900">Search & Filter</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Filter by date range or search by INSP # / inspection type.
                            </p>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('inspections.index') }}" class="mt-5">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-xs font-semibold text-gray-600">Search</label>
                                <input type="text"
                                       name="q"
                                       value="{{ request('q') }}"
                                       placeholder="INSP-000123, Daily Inspection…"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-600">Date from</label>
                                <input type="date"
                                       name="date_from"
                                       value="{{ request('date_from') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            </div>

                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-600">Date to</label>
                                <input type="date"
                                       name="date_to"
                                       value="{{ request('date_to') }}"
                                       class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm">
                            </div>

                            <div class="md:col-span-1 flex gap-2">
                                <button type="submit"
                                        class="w-full inline-flex justify-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-black">
                                    Apply
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Filters persist through pagination automatically.
                            </div>

                            @if(request()->filled('q') || request()->filled('date_from') || request()->filled('date_to'))
                                <a href="{{ route('inspections.index') }}"
                                   class="text-sm font-semibold text-gray-700 hover:text-gray-900 hover:underline">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr class="text-xs uppercase tracking-wide text-gray-600">
                                <th class="py-3.5 pr-3 pl-4 text-left font-semibold sm:pl-6">INSP #</th>
                                <th class="px-3 py-3.5 text-left font-semibold">Date</th>
                                <th class="px-3 py-3.5 text-left font-semibold">Overall</th>
                                <th class="px-3 py-3.5 text-left font-semibold">Times</th>
                                <th class="px-3 py-3.5 text-left font-semibold">Status</th>
                                <th class="px-3 py-3.5 text-left font-semibold">Inspector</th>
                                <th class="py-3.5 pr-4 pl-3 text-right font-semibold sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($inspections as $inspection)
                                @php
                                    $header = $getHeader($inspection);

                                    $overall = $header['overall_status'] ?? null;
                                    $overallBadge = $overall === 'Satisfactory'
                                        ? $badge('Satisfactory', 'green')
                                        : ($overall === 'Unsatisfactory' ? $badge('Unsatisfactory', 'red') : $badge('—', 'gray'));

                                    $status = $inspection->status ?? 'Open';
                                    $statusBadge = match ($status) {
                                        'Certified' => $badge('Certified', 'green'),
                                        'Completed' => $badge('Completed', 'green'),
                                        'Issues Noted' => $badge('Issues Noted', 'yellow'),
                                        'Work Orders Created' => $badge('WO Created', 'indigo'),
                                        'Closed' => $badge('Closed', 'gray'),
                                        default => $badge($status, 'blue'),
                                    };

                                    $crash = $fmtTime($header['crash_phone_test_time'] ?? null);
                                    $am = $fmtTime($header['am_time'] ?? null);
                                    $pm = $fmtTime($header['pm_time'] ?? null);
                                    $other = $fmtTime($header['other_time'] ?? null);

                                    $type = $inspection->inspection_type ?? 'FAA Part 139 - Daily Inspection';

                                    $locked = (bool)($inspection->is_locked ?? false);
                                    $lockedBadge = $locked ? $badge('Locked', 'gray') : $badge('Editable', 'blue');
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    {{-- INSP --}}
                                    <td class="py-5 pr-3 pl-4 text-sm sm:pl-6">
                                        <div class="flex flex-col">
                                            <a href="{{ route('inspections.show', $inspection) }}"
                                               class="font-semibold text-blue-700 hover:underline">
                                                {{ $inspection->insp_number }}
                                            </a>
                                            <div class="mt-1 flex items-center gap-2 flex-wrap">
                                                <span class="text-xs text-gray-500">{{ $type }}</span>
                                                {!! $lockedBadge !!}
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-3 py-5 text-sm text-gray-900 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $fmtDate($inspection->inspection_date) }}</span>
                                            <span class="text-xs text-gray-500">{{ $header['inspection_day'] ?? '—' }}</span>
                                        </div>
                                    </td>

                                    {{-- Overall --}}
                                    <td class="px-3 py-5 whitespace-nowrap">
                                        {!! $overallBadge !!}
                                    </td>

                                    {{-- Times --}}
                                    <td class="px-3 py-5">
                                        <div class="text-xs text-gray-700 grid grid-cols-2 gap-x-6 gap-y-1">
                                            <div><span class="font-semibold">Crash:</span> {{ $crash }}</div>
                                            <div><span class="font-semibold">Other:</span> {{ $other }}</div>
                                            <div><span class="font-semibold">AM:</span> {{ $am }}</div>
                                            <div><span class="font-semibold">PM:</span> {{ $pm }}</div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-3 py-5 whitespace-nowrap">
                                        {!! $statusBadge !!}
                                    </td>

                                    {{-- Inspector --}}
                                    <td class="px-3 py-5 text-sm text-gray-900">
                                        <div class="flex flex-col">
                                            <span class="font-semibold">{{ $inspection->inspector?->name ?? '—' }}</span>
                                            <span class="text-xs text-gray-500">
                                                Created {{ $inspection->created_at?->format('Y-m-d H:i') ?? '—' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="py-5 pr-4 pl-3 text-right text-sm font-semibold whitespace-nowrap sm:pr-6">
                                        <div class="inline-flex items-center gap-2 flex-wrap justify-end">
                                            <a href="{{ route('inspections.show', $inspection) }}"
                                               class="inline-flex items-center rounded-md border border-gray-300 px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-100">
                                                View
                                            </a>

                                            <a href="{{ route('inspections.print', $inspection) }}"
                                               target="_blank"
                                               class="inline-flex items-center rounded-md border border-gray-300 px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-100">
                                                Print
                                            </a>

                                            <a href="{{ route('inspections.pdf', $inspection) }}"
                                               class="inline-flex items-center rounded-md border border-gray-300 px-2.5 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-100">
                                                PDF
                                            </a>

                                            @can('update', $inspection)
                                                @if(!$locked)
                                                    <a href="{{ route('inspections.edit', $inspection) }}"
                                                       class="inline-flex items-center rounded-md border border-indigo-200 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                        Edit
                                                    </a>
                                                @else
                                                    <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-semibold text-gray-500">
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
                                                            class="inline-flex items-center rounded-md border border-red-200 bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-500">
                                        No inspections logged yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-200">
                    {{ $inspections->links() }}
                </div>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
