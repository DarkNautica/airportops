<x-layouts.sidebar-app>
    <x-slot name="title">Pass Alongs</x-slot>

    <x-slot name="actions">
        @can('create', App\Models\PassAlong::class)
            <a href="{{ route('pass-alongs.create') }}"
               class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-black">
                + New Pass Along
            </a>
        @endcan
    </x-slot>

    <div class="py-10 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">
                            Daily Checklist & Pass-Along
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Draft and submitted pass along records.
                        </p>
                    </div>

                    <form method="GET" class="flex gap-2">
                        <input type="text"
                               name="q"
                               value="{{ request('q') }}"
                               placeholder="Search by specialist, date, notes…"
                               class="w-72 rounded-md border-gray-300 text-sm shadow-sm focus:border-gray-900 focus:ring-gray-900">
                        <button class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-semibold hover:bg-gray-50">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wide text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Date</th>
                            <th class="px-4 py-3 text-left font-semibold">Specialist</th>
                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Lock</th>
                            <th class="px-4 py-3 text-right font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($passAlongs as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ optional($p->date)->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">{{ $p->specialist_name ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 rounded border text-xs font-semibold
                                        {{ $p->status === 'submitted'
                                            ? 'bg-green-100 text-green-800 border-green-200'
                                            : 'bg-gray-100 text-gray-800 border-gray-200' }}">
                                        {{ strtoupper($p->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 rounded border text-xs font-semibold
                                        {{ $p->is_locked
                                            ? 'bg-green-100 text-green-800 border-green-200'
                                            : 'bg-yellow-100 text-yellow-800 border-yellow-200' }}">
                                        {{ $p->is_locked ? 'LOCKED' : 'OPEN' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('pass-alongs.show', $p) }}"
                                       class="font-semibold text-blue-700 hover:underline">
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
