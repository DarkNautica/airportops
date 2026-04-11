<x-layouts.sidebar-app>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Pass Alongs</h1>
    </x-slot>

    <x-slot name="actions">
        @can('create', App\Models\PassAlong::class)
            <a href="{{ route('pass-alongs.create') }}"
               class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                <span class="text-base leading-none">+</span> New Pass Along
            </a>
        @endcan
    </x-slot>

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            {{-- Search / Filter --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">SEARCH</span>
                </div>
                <div class="p-4">
                    <form method="GET" class="flex flex-col sm:flex-row sm:items-end gap-3">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keyword</label>
                            <input type="text"
                                   name="q"
                                   value="{{ request('q') }}"
                                   placeholder="Search by specialist, date, notes..."
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        </div>
                        <button class="inline-flex items-center px-4 py-2.5 rounded-lg border border-surface-border bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 shadow-sm">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">PASS ALONGS</span>
                    <span class="font-mono text-[11px] text-slate-400">{{ $passAlongs->total() }} total</span>
                </div>

                <table class="c139-table min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Specialist</th>
                            <th>Status</th>
                            <th>Lock</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($passAlongs as $p)
                            <tr>
                                <td class="font-mono text-[11px]">{{ optional($p->date)->format('Y-m-d') }}</td>
                                <td>{{ $p->specialist_name ?: '—' }}</td>
                                <td>
                                    @if($p->status === 'submitted')
                                        <x-badge variant="certified">Submitted</x-badge>
                                    @else
                                        <x-badge variant="draft">Draft</x-badge>
                                    @endif
                                </td>
                                <td>
                                    @if($p->is_locked)
                                        <x-badge variant="locked">Locked</x-badge>
                                    @else
                                        <x-badge variant="open">Open</x-badge>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('pass-alongs.show', $p) }}"
                                       class="font-semibold text-blue-700 hover:underline text-sm">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                    No pass along records yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $passAlongs->links() }}

        </div>
    </div>
</x-layouts.sidebar-app>
