{{-- resources/views/work_orders/index.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Work Orders</h1>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('work-orders.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-black transition">
            + New Work Order
        </a>
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

    <div class="py-8">
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
                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="border-t-4 border-[#2563EB] px-5 pt-4 pb-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Open</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['open'] ?? 0 }}</div>
                        <div class="mt-2 text-xs text-slate-400">Status = Open</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="border-t-4 border-[#DC2626] px-5 pt-4 pb-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Critical Open</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['critical'] ?? 0 }}</div>
                        <div class="mt-2 text-xs text-slate-400">Priority = Critical (not closed)</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="border-t-4 border-[#D97706] px-5 pt-4 pb-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Overdue</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['overdue'] ?? 0 }}</div>
                        <div class="mt-2 text-xs text-slate-400">Due date passed (not closed)</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="border-t-4 border-[#16A34A] px-5 pt-4 pb-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Completed / Closed</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ $summary['completed'] ?? 0 }}</div>
                        <div class="mt-2 text-xs text-slate-400">Completed + Closed</div>
                    </div>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">

                {{-- Panel Header + Filters --}}
                <div class="px-6 py-5 border-b border-surface-border">
                    <div class="panel-header">
                        <span class="panel-header-label">ALL WORK ORDERS</span>
                    </div>

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('work-orders.index') }}" class="mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            <div class="md:col-span-5">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input
                                    type="text"
                                    name="q"
                                    value="{{ $qVal }}"
                                    placeholder="WO-000123, Light, RWY, Taxiway, etc."
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select
                                    name="status"
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                >
                                    <option value="all" {{ $statusVal === 'all' ? 'selected' : '' }}>All</option>
                                    @foreach (\App\Enums\WorkOrderStatus::all() as $s)
                                        <option value="{{ $s }}" {{ $statusVal === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                                <select
                                    name="priority"
                                    class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"
                                >
                                    <option value="all" {{ $priorityVal === 'all' ? 'selected' : '' }}>All</option>
                                    @foreach (\App\Enums\WorkOrderPriority::all() as $p)
                                        <option value="{{ $p }}" {{ $priorityVal === $p ? 'selected' : '' }}>{{ $p }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Overdue only</label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700 mt-1">
                                    <input
                                        type="checkbox"
                                        name="overdue"
                                        value="1"
                                        {{ $overdueVal ? 'checked' : '' }}
                                        class="rounded border-surface-border text-blue-600 focus:ring-blue-500"
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
                                    class="w-full inline-flex justify-center rounded-lg bg-gray-900 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-black transition"
                                >
                                    Apply
                                </button>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-xs text-slate-400">
                                Tip: sorting + filters persist through pagination automatically.
                            </div>

                            <a href="{{ route('work-orders.index') }}"
                               class="text-sm font-semibold text-[#2563EB] hover:text-[#3B82F6]">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="c139-table min-w-full">
                        <thead>
                            <tr>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    @php $col = 'id'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                                    <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:text-slate-700">
                                        ID <span class="text-[10px]">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                                    </a>
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    @php $col = 'wo_number'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                                    <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:text-slate-700">
                                        WO # <span class="text-[10px]">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                                    </a>
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    Title
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    Location
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    @php $col = 'priority'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                                    <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:text-slate-700">
                                        Priority <span class="text-[10px]">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                                    </a>
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    @php $col = 'status'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                                    <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:text-slate-700">
                                        Status <span class="text-[10px]">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                                    </a>
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    @php $col = 'due_date'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                                    <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:text-slate-700">
                                        Due <span class="text-[10px]">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                                    </a>
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">
                                    @php $col = 'created_at'; $dir = next_direction($col, $currentSort, $currentDirection); @endphp
                                    <a href="{{ sort_url($col, $dir) }}" class="inline-flex items-center gap-1 hover:text-slate-700">
                                        Created <span class="text-[10px]">{{ sort_icon($col, $currentSort, $currentDirection) }}</span>
                                    </a>
                                </th>

                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-right">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-surface-border">
                            @forelse ($workOrders as $workOrder)
                                @php
                                    $statusTxt = $workOrder->status ?? '—';
                                    $statusBadge = match ($statusTxt) {
                                        'Open' => 'open',
                                        'In Progress' => 'in_progress',
                                        'Completed', 'Closed' => 'green',
                                        'On Hold' => 'neutral',
                                        default => 'neutral',
                                    };

                                    $priorityTxt = $workOrder->priority ?? '—';
                                    $priorityBadge = match ($priorityTxt) {
                                        'Critical' => 'critical',
                                        'High' => 'warning',
                                        'Medium', 'Normal' => 'open',
                                        'Low' => 'neutral',
                                        default => 'neutral',
                                    };

                                    $priorityBorderColor = match ($priorityTxt) {
                                        'Critical' => '#DC2626',
                                        'High' => '#D97706',
                                        'Medium', 'Normal' => '#2563EB',
                                        'Low' => '#94A3B8',
                                        default => '#94A3B8',
                                    };

                                    $due = $workOrder->due_date ? \Illuminate\Support\Carbon::parse($workOrder->due_date) : null;
                                    $isOverdue = $due && $due->isPast() && !in_array($statusTxt, ['Completed','Closed'], true);
                                @endphp

                                <tr class="hover:bg-[#F8FAFC] transition" style="border-left: 4px solid {{ $priorityBorderColor }};">
                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        <span class="font-mono text-[11px] text-slate-500">{{ $workOrder->id }}</span>
                                    </td>

                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        <a href="{{ route('work-orders.show', $workOrder) }}"
                                           class="font-mono font-semibold text-[#2563EB] hover:text-[#3B82F6]">
                                            {{ $workOrder->wo_number ?? '—' }}
                                        </a>
                                    </td>

                                    <td class="px-4 py-2.5 text-sm">
                                        <div class="font-semibold text-gray-900">
                                            <a href="{{ route('work-orders.show', $workOrder) }}" class="hover:text-[#2563EB]">
                                                {{ $workOrder->title }}
                                            </a>
                                        </div>
                                        @if(!empty($workOrder->description))
                                            <div class="mt-0.5 text-xs text-slate-400 line-clamp-1">
                                                {{ $workOrder->description }}
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap text-gray-700">
                                        {{ $workOrder->location ?? '—' }}
                                    </td>

                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        <x-badge variant="{{ $priorityBadge }}">{{ $priorityTxt }}</x-badge>
                                    </td>

                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        <x-badge variant="{{ $statusBadge }}">{{ $statusTxt }}</x-badge>
                                    </td>

                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        @if($due)
                                            <div class="font-mono text-[11px] font-semibold {{ $isOverdue ? 'text-red-700' : 'text-gray-900' }}">
                                                {{ $due->format('Y-m-d') }}
                                            </div>
                                            @if($isOverdue)
                                                <x-badge variant="overdue">Overdue</x-badge>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2.5 text-sm whitespace-nowrap">
                                        <span class="font-mono text-[11px] text-slate-500">{{ $workOrder->created_at?->format('Y-m-d H:i') ?? '—' }}</span>
                                    </td>

                                    <td class="px-4 py-2.5 text-right text-sm font-semibold whitespace-nowrap">
                                        <a href="{{ route('work-orders.edit', $workOrder) }}"
                                           class="text-[#2563EB] hover:text-[#3B82F6]">
                                            Edit
                                        </a>

                                        <span class="text-slate-300 mx-1.5">|</span>

                                        <form action="{{ route('work-orders.destroy', $workOrder) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Delete this work order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-800">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-sm text-slate-400">
                                        No work orders yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-4 py-4 border-t border-surface-border">
                    {{ $workOrders->links() }}
                </div>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
