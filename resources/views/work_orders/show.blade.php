{{-- resources/views/work_orders/show.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <div>
            <div class="font-mono text-[11px] text-slate-500">{{ $workOrder->wo_number }}</div>
            <h1 class="font-instrument text-xl text-gray-900">{{ $workOrder->title }}</h1>
        </div>
    </x-slot>

    <x-slot name="actions">
        <a href="{{ route('work-orders.edit', $workOrder) }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-black transition">
            Edit
        </a>
        <a href="{{ route('work-orders.index') }}"
           class="inline-flex items-center gap-1.5 rounded-lg border border-surface-border bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
            Back
        </a>
    </x-slot>

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

        $due = $workOrder->due_date ? \Illuminate\Support\Carbon::parse($workOrder->due_date) : null;
        $isOverdue = $due && $due->isPast() && !in_array($statusTxt, ['Completed','Closed'], true);
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-6 lg:px-8 space-y-6">

            {{-- Details Card --}}
            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="px-6 py-4 border-b border-surface-border flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <x-badge variant="{{ $statusBadge }}">{{ $statusTxt }}</x-badge>
                        <x-badge variant="{{ $priorityBadge }}">{{ $priorityTxt }}</x-badge>

                        @if($isOverdue)
                            <x-badge variant="overdue">Overdue</x-badge>
                        @endif
                    </div>

                    <div class="font-mono text-[11px] text-slate-500">
                        Created: {{ $workOrder->created_at?->format('Y-m-d H:i') ?? '—' }}
                        <span class="mx-2 text-slate-300">|</span>
                        Updated: {{ $workOrder->updated_at?->format('Y-m-d H:i') ?? '—' }}
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $workOrder->location ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Due Date</dt>
                            <dd class="mt-1 text-sm font-semibold {{ $isOverdue ? 'text-red-700' : 'text-gray-900' }}">
                                @if($due)
                                    <span class="font-mono text-[11px]">{{ $due->format('Y-m-d') }}</span>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Description</dt>
                            <dd class="mt-2 text-sm text-gray-900 whitespace-pre-line">
                                {{ $workOrder->description ?? 'No description provided.' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- AUDIT TRAIL --}}
            @php
                $humanKey = fn(string $k) => str($k)->replace('_', ' ')->headline();
                $fmt = fn($v) => is_null($v) ? '—' : (is_bool($v) ? ($v ? 'true' : 'false') : (is_array($v) ? '[json]' : (string)$v));
                $ignoreKeys = ['created_at','updated_at'];
            @endphp

            <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                <div class="panel-header px-6 py-4 border-b border-surface-border flex items-center justify-between">
                    <span class="panel-header-label">AUDIT TRAIL</span>
                    <span class="font-mono text-[11px] text-slate-400">
                        {{ $auditLogs->count() }} event{{ $auditLogs->count() === 1 ? '' : 's' }}
                    </span>
                </div>

                <div class="p-6">
                    <ol class="space-y-4">
                        @foreach($auditLogs as $log)
                            @php
                                $evt = $log->event ?? 'event';

                                $evtBadge = match ($evt) {
                                    'created' => 'green',
                                    'updated' => 'open',
                                    'status_changed' => 'warning',
                                    'deleted' => 'critical',
                                    default => 'neutral',
                                };

                                $props = is_array($log->properties ?? null) ? $log->properties : [];
                                $old = $props['old'] ?? [];
                                $new = $props['new'] ?? [];
                                $keys = array_values(array_diff(array_unique(array_merge(array_keys($old), array_keys($new))), $ignoreKeys));
                            @endphp

                            <li class="bg-white rounded-xl border border-surface-border p-4 hover:shadow-md transition">
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <x-badge variant="{{ $evtBadge }}">{{ strtoupper(str_replace('_',' ',$evt)) }}</x-badge>
                                    <span class="font-mono text-[11px] text-slate-500">{{ $log->created_at?->format('Y-m-d H:i:s') }}</span>
                                    <span class="text-slate-300">•</span>
                                    <span class="font-semibold text-gray-700">
                                        {{ $log->causer?->name ?? 'System' }}
                                    </span>
                                    @if($log->ip)
                                        <span class="text-slate-300">|</span>
                                        <span class="font-mono text-[11px] text-slate-500">IP {{ $log->ip }}</span>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    @if(empty($keys))
                                        <div class="text-sm text-slate-400">No field-level details recorded.</div>
                                    @else
                                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Key changes</div>
                                        <ul class="mt-2 space-y-1 text-sm">
                                            @foreach($keys as $k)
                                                <li>
                                                    <span class="font-semibold text-gray-900">{{ $humanKey($k) }}:</span>
                                                    <span class="text-slate-500">{{ $fmt($old[$k] ?? null) }}</span>
                                                    <span class="mx-1 text-slate-300 font-black">&rarr;</span>
                                                    <span class="text-gray-900">{{ $fmt($new[$k] ?? null) }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

        </div>
    </div>
</x-sidebar-app-layout>
