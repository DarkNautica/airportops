{{-- resources/views/work_orders/index.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-900 leading-tight">Work Orders</h2>
            <p class="text-sm text-gray-600 mt-1">Track issues, prioritize work, and close the loop.</p>
        </div>
    </x-slot>

    @php
        // From controller (with safe fallbacks)
        $currentSort = $sort ?? 'created_at';
        $currentDirection = $direction ?? 'desc';

        $qVal = $q ?? request('q', '');
        $statusVal = $status ?? request('status', 'all');
        $priorityVal = $priority ?? request('priority', 'all');
        $overdueVal = isset($overdue) ? (bool)$overdue : (bool)request('overdue', false);

        $summary = $summary ?? ['open' => 0, 'critical' => 0, 'overdue' => 0, 'completed' => 0];

        function next_direction($column, $currentSort, $currentDirection) {
            if ($currentSort === $column) return $currentDirection === 'asc' ? 'desc' : 'asc';
            return 'asc';
        }

        function sort_icon($column, $currentSort, $currentDirection) {
            if ($currentSort !== $column) return '';
            return $currentDirection === 'asc' ? '↑' : '↓';
        }

        function sort_url($col, $dir) {
            return request()->fullUrlWithQuery(['sort' => $col, 'direction' => $dir]);
        }
    @endphp

    <div class="py-10 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="w-full px-6 lg:px-10 space-y-6">

            {{-- Flash message --}}
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-900 shadow-sm">
                    <div class="font-semibold">Success</div>
                    <div class="text-sm mt-1">{{ session('success') }}</div>
                </div>
            @endif

            {{-- KPI Strip --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500">Open</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['open'] ?? 0 }}</div>
                    <div class="mt-3 text-xs text-gray-500">Status = Open</div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500">Critical Open</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['critical'] ?? 0 }}</div>
                    <div class="mt-3 text-xs text-gray-500">Priority = Critical (not closed)</div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500">Overdue</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['overdue'] ?? 0 }}</div>
                    <div class="mt-3 text-xs text-gray-500">Due date passed (not closed)</div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="text-xs font-semibold text-gray-500">Completed / Closed</div>
                    <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['completed'] ?? 0 }}</div>
                    <div class="mt-3 text-xs text-gray-500">Completed + Closed</div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                {{-- Header --}}
                <div class="px-4 sm:px-6 lg:px-8 py-6 border-b border-gray-200">
                    <div class="sm:flex sm:items-center sm:justify-between gap-4">
                        <div class="sm:flex-auto">
                            <h1 class="text-base font-semibold text-gray-900">All Work Orders</h1>
                            <p class="mt-2 text-sm text-gray-600">
                                Search, filter, and sort. Everything stays readable even at 2am on the ramp.
                            </p>
                        </div>

                        <div class="mt-4 sm:mt-0 sm:flex-none flex items-center gap-2">
                            <a href="{{ route('work-orders.create') }}"
                               class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-black">
                                + New Work Order
                            </a>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('work-orders.index') }}" class="mt-5">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-xs font-semibold text-gray-600">Search</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $qVal }}"
                                    placeholder="WO-000123, Light, RWY, Taxiway, etc."
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600">Status</label>
                                <select
                                    name="status"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                >
                                    <option value="all" {{ $statusVal === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="Open" {{ $statusVal === 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="In Progress" {{ $statusVal === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Completed" {{ $statusVal === 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Closed" {{ $statusVal === 'Closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600">Priority</label>
                                <select
                                    name="priority"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-gray-900 focus:ring-gray-900 text-sm"
                                >
                                    <option value="all" {{ $priorityVal === 'all' ? 'selected' : '' }}>All</option>
                                    <option value="Critical" {{ $priorityVal === 'Critical' ? 'selected' : '' }}>Critical</option>
                                    <option value="High" {{ $priorityVal === 'High' ? 'selected' : '' }}>High</option>
                                    <option value="Medium" {{ $priorityVal === 'Medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="Normal" {{ $priorityVal === 'Normal' ? 'selected' : '' }}>Normal</option>
                                    <option value="Low" {{ $priorityVal === 'Low' ? 'selected' : '' }}>Low</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600">Overdue only</label>
                                <label class="mt-2 inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        name="overdue"
                                        value="1"
                                        {{ $overdueVal ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-gray-900 focus:ring-gray-900"
                                    />
                                    Show overdue
                                </label>
                            </div>

                            <div class="md:col-span-1 flex gap-2">
                                {{-- Preserve sort while filtering --}}
                                <input type="hidden" name="sort" value="{{ $currentSort }}">
                                <input type="hidden" name="direction" value="{{ $currentDirection }}">

                                <button
                                    type="submit"
                                    class="w-full inline-flex justify-center rounded-md bg-gray-900 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-black"
                                >
                                    Apply
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-xs text-gray-500">
                                Tip: sorting + filters persist through pagination automatically.
                            </div>

                            <a href="{{ route('work-orders.index') }}"
                               class="text-sm font-semibold text-gray-700 hover:text-gray-900 hover:underline">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
<div class="flow-root">
    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="block w-full min-w-full py-2 align-middle sm:px-6 lg:px-8">

            <table class="relative min-w-full divide-y divide-gray-300">
                <thead>
                    <tr>
                        <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900">
                            @php $col = 'id'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                            <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:underline">
                                ID <span class="text-xs text-gray-500">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                            </a>
                        </th>

                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            @php $col = 'wo_number'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                            <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:underline">
                                WO # <span class="text-xs text-gray-500">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                            </a>
                        </th>

                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Title
                        </th>

                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Location
                        </th>

                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            @php $col = 'priority'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                            <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:underline">
                                Priority <span class="text-xs text-gray-500">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                            </a>
                        </th>

                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            @php $col = 'status'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                            <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:underline">
                                Status <span class="text-xs text-gray-500">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                            </a>
                        </th>

                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            @php $col = 'due_date'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                            <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:underline">
                                Due <span class="text-xs text-gray-500">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                            </a>
                        </th>

                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                            @php $col = 'created_at'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                            <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:underline">
                                Created <span class="text-xs text-gray-500">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                            </a>
                        </th>

                        <th scope="col" class="py-3.5 pr-6 pl-3 text-right">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($workOrders as $workOrder)
                        @php
                            $statusTxt = $workOrder->status ?? '—';
                            $statusPill = match ($statusTxt) {
                                'Open' => 'bg-red-50 text-red-700 ring-red-600/20',
                                'In Progress' => 'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
                                'Completed', 'Closed' => 'bg-green-50 text-green-700 ring-green-600/20',
                                default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                            };

                            $priorityTxt = $workOrder->priority ?? '—';
                            $priorityPill = match ($priorityTxt) {
                                'Critical' => 'bg-red-50 text-red-700 ring-red-600/20',
                                'High' => 'bg-orange-50 text-orange-700 ring-orange-600/20',
                                'Medium', 'Normal' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                'Low' => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                            };

                            $due = $workOrder->due_date ? \Illuminate\Support\Carbon::parse($workOrder->due_date) : null;
                            $isOverdue = $due && $due->isPast() && !in_array($statusTxt, ['Completed','Closed'], true);
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="py-5 pr-3 pl-4 text-sm whitespace-nowrap text-gray-900">
                                {{ $workOrder->id }}
                            </td>

                            <td class="px-3 py-5 text-sm whitespace-nowrap">
                                <a href="{{ route('work-orders.show', $workOrder) }}"
                                   class="font-semibold text-blue-700 hover:underline">
                                    {{ $workOrder->wo_number ?? '—' }}
                                </a>
                            </td>

                            <td class="px-3 py-5 text-sm">
                                <div class="font-semibold text-gray-900">
                                    <a href="{{ route('work-orders.show', $workOrder) }}" class="hover:underline">
                                        {{ $workOrder->title }}
                                    </a>
                                </div>
                                @if(!empty($workOrder->description))
                                    <div class="mt-1 text-xs text-gray-500 line-clamp-1">
                                        {{ $workOrder->description }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-3 py-5 text-sm whitespace-nowrap text-gray-700">
                                {{ $workOrder->location ?? '—' }}
                            </td>

                            <td class="px-3 py-5 text-sm whitespace-nowrap">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $priorityPill }}">
                                    {{ $priorityTxt }}
                                </span>
                            </td>

                            <td class="px-3 py-5 text-sm whitespace-nowrap">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusPill }}">
                                    {{ $statusTxt }}
                                </span>
                            </td>

                            <td class="px-3 py-5 text-sm whitespace-nowrap">
                                @if($due)
                                    <div class="font-semibold {{ $isOverdue ? 'text-red-700' : 'text-gray-900' }}">
                                        {{ $due->format('Y-m-d') }}
                                    </div>
                                    @if($isOverdue)
                                        <div class="text-xs text-red-600">Overdue</div>
                                    @endif
                                @else
                                    <span class="text-gray-500">—</span>
                                @endif
                            </td>

                            <td class="px-3 py-5 text-sm whitespace-nowrap text-gray-700">
                                {{ $workOrder->created_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>

                            <td class="py-5 pr-6 pl-3 text-right text-sm font-semibold whitespace-nowrap">
                                <a href="{{ route('work-orders.edit', $workOrder) }}"
                                   class="text-indigo-600 hover:text-indigo-900 hover:underline">
                                    Edit
                                </a>

                                <span class="text-gray-300 mx-2">|</span>

                                <form action="{{ route('work-orders.destroy', $workOrder) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Delete this work order?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-800 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500">
                                No work orders yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>


                            {{-- Pagination --}}
                            <div class="px-2 py-4">
                                {{ $workOrders->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
