<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="font-instrument text-xl text-gray-900">NOTAMs</h1>
            <a href="{{ route('notams.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                New NOTAM
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash --}}
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header">
                    <span class="panel-header-label">FILTERS</span>
                </div>
                <form method="GET" class="p-4 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="q" value="{{ $q ?? '' }}"
                               placeholder="NOTAM number / subject / text..."
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Station</label>
                        <input type="text" name="station" value="{{ $station ?? 'KAVL' }}"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                                class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            @php $st = $status ?? 'Active'; @endphp
                            @foreach (['Active','Draft','Expired','Cancelled'] as $opt)
                                <option value="{{ $opt }}" @selected($st === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category"
                                class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="">All</option>
                            @foreach (['Runway','Taxiway','Apron/Ramp','Lighting','NAVAID','Obstruction/Crane','Construction','Other'] as $opt)
                                <option value="{{ $opt }}" @selected(($category ?? null) === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-12 flex flex-wrap gap-2 justify-end pt-1">
                        <a href="{{ route('notams.index') }}"
                           class="inline-flex items-center rounded-lg border border-surface-border px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Reset
                        </a>
                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 transition-colors">
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header flex items-center justify-between">
                    <span class="panel-header-label">NOTAMS</span>
                    <span class="font-mono text-[11px] text-slate-400">{{ $notams->total() }} total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="c139-table min-w-full">
                        <thead>
                            <tr class="bg-[#F8FAFC] border-b border-surface-border">
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">NOTAM #</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Station</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Category</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Subject</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Effective</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Status</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @forelse ($notams as $n)
                                @php
                                    $status = $n->status ?? '—';
                                    $badgeVariant = match ($status) {
                                        'Active' => 'active',
                                        'Draft' => 'draft',
                                        'Expired' => 'warning',
                                        'Cancelled' => 'cancelled',
                                        default => 'neutral',
                                    };
                                    $stripColor = match ($status) {
                                        'Active' => '#16A34A',
                                        'Draft' => '#2563EB',
                                        'Expired' => '#D97706',
                                        'Cancelled' => '#94A3B8',
                                        default => '#94A3B8',
                                    };
                                @endphp

                                <tr class="hover:bg-[#F8FAFC] transition-colors" style="border-left: 4px solid {{ $stripColor }};">
                                    <td class="px-4 py-2.5 text-sm">
                                        <a href="{{ route('notams.show', $n) }}" class="text-[#2563EB] hover:text-[#3B82F6] font-mono font-semibold">
                                            {{ $n->notam_number }}
                                        </a>
                                        @if($n->is_locked)
                                            <x-badge variant="locked" :dot="false" class="ml-1.5 text-[10px]">LOCKED</x-badge>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2.5 text-sm text-gray-900">{{ $n->station }}</td>
                                    <td class="px-4 py-2.5 text-sm text-gray-900">{{ $n->category }}</td>
                                    <td class="px-4 py-2.5 text-sm text-gray-900">
                                        <div class="font-semibold">{{ $n->subject ?? '—' }}</div>
                                        <div class="text-xs text-slate-400 line-clamp-1">
                                            {{ \Illuminate\Support\Str::limit($n->notam_text, 80) }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-2.5 text-sm">
                                        <div class="font-mono text-[11px] text-slate-500">From</div>
                                        <div class="font-mono text-[11px]">{{ $n->effective_from?->format('Y-m-d H:i') ?? '—' }}Z</div>
                                        <div class="font-mono text-[11px] text-slate-500 mt-0.5">To</div>
                                        <div class="font-mono text-[11px]">{{ $n->effective_to?->format('Y-m-d H:i') ?? '—' }}Z</div>
                                    </td>

                                    <td class="px-4 py-2.5">
                                        <x-badge :variant="$badgeVariant">{{ $status }}</x-badge>
                                    </td>

                                    <td class="px-4 py-2.5 text-right">
                                        <a href="{{ route('notams.show', $n) }}" class="text-[#2563EB] hover:text-[#3B82F6] text-sm font-medium">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-sm text-slate-400">
                                        No NOTAMs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-surface-border">
                    {{ $notams->links() }}
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app-layout>
