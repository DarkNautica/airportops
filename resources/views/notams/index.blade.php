<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">NOTAM Tracker</h2>
                <p class="text-sm text-gray-500 mt-1">Internal tracking + official feed sync-ready.</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('notams.create') }}"
                   class="px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                    + New NOTAM
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Flash --}}
            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-900">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-900">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Filters --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-4">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Search</label>
                        <input type="text" name="q" value="{{ $q ?? '' }}"
                               placeholder="NOTAM number / subject / text…"
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Station</label>
                        <input type="text" name="station" value="{{ $station ?? 'KAVL' }}"
                               class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</label>
                        <select name="status"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                            @php $st = $status ?? 'Active'; @endphp
                            @foreach (['Active','Draft','Expired','Cancelled'] as $opt)
                                <option value="{{ $opt }}" @selected($st === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Category</label>
                        <select name="category"
                                class="mt-1 w-full rounded-md border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">All</option>
                            @foreach (['Runway','Taxiway','Apron/Ramp','Lighting','NAVAID','Obstruction/Crane','Construction','Other'] as $opt)
                                <option value="{{ $opt }}" @selected(($category ?? null) === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-12 flex flex-wrap gap-2 justify-end pt-2">
                        <a href="{{ route('notams.index') }}"
                           class="px-3 py-2 rounded-md border text-sm text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                        <button type="submit"
                                class="px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                            Apply
                        </button>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <div class="font-semibold text-gray-900">NOTAMs</div>
                        <div class="text-xs text-gray-500">Filtered results</div>
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $notams->total() }} total
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold">NOTAM #</th>
                                <th class="px-4 py-3 text-left font-semibold">Station</th>
                                <th class="px-4 py-3 text-left font-semibold">Category</th>
                                <th class="px-4 py-3 text-left font-semibold">Subject</th>
                                <th class="px-4 py-3 text-left font-semibold">Effective</th>
                                <th class="px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-4 py-3 text-right font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($notams as $n)
                                @php
                                    $status = $n->status ?? '—';
                                    $badge = match ($status) {
                                        'Active' => 'bg-green-100 text-green-800',
                                        'Draft' => 'bg-gray-100 text-gray-800',
                                        'Expired' => 'bg-yellow-100 text-yellow-800',
                                        'Cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };

                                    $lockBadge = $n->is_locked ? 'bg-gray-900 text-white' : 'bg-transparent text-gray-500';
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-semibold text-blue-700">
                                        <a href="{{ route('notams.show', $n) }}" class="hover:underline">
                                            {{ $n->notam_number }}
                                        </a>
                                        @if($n->is_locked)
                                            <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $lockBadge }}">
                                                LOCKED
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-gray-900">{{ $n->station }}</td>
                                    <td class="px-4 py-3 text-gray-900">{{ $n->category }}</td>
                                    <td class="px-4 py-3 text-gray-900">
                                        <div class="font-semibold">{{ $n->subject ?? '—' }}</div>
                                        <div class="text-xs text-gray-500 line-clamp-1">
                                            {{ \Illuminate\Support\Str::limit($n->notam_text, 80) }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-gray-900">
                                        <div class="text-xs text-gray-500">From</div>
                                        <div>{{ $n->effective_from?->format('Y-m-d H:i') ?? '—' }}Z</div>
                                        <div class="text-xs text-gray-500 mt-1">To</div>
                                        <div>{{ $n->effective_to?->format('Y-m-d H:i') ?? '—' }}Z</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold {{ $badge }}">
                                            {{ $status }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('notams.show', $n) }}" class="text-gray-700 hover:underline">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                        No NOTAMs found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-5 py-4 border-t border-gray-200">
                    {{ $notams->links() }}
                </div>
            </div>
        </div>
    </div>
</x-sidebar-app-layout>
