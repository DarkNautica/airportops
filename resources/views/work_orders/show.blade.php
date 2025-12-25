<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="min-w-0">
                <div class="text-xs text-gray-500">Work Order</div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight truncate">
                    {{ $workOrder->wo_number }} — {{ $workOrder->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    View details, status, and audit history.
                </p>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('work-orders.edit', $workOrder) }}"
                   class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 shadow-sm">
                    Edit
                </a>

                <a href="{{ route('work-orders.index') }}"
                   class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50 shadow-sm">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gradient-to-b from-gray-50 to-gray-100">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Details Card --}}
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusPill }}">
                            {{ $statusTxt }}
                        </span>
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $priorityPill }}">
                            {{ $priorityTxt }}
                        </span>

                        @if($isOverdue)
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset bg-red-50 text-red-700 ring-red-600/20">
                                Overdue
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-gray-500">
                        Created: {{ $workOrder->created_at?->format('Y-m-d H:i') ?? '—' }}
                        <span class="mx-2 text-gray-300">|</span>
                        Updated: {{ $workOrder->updated_at?->format('Y-m-d H:i') ?? '—' }}
                    </div>
                </div>

                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Location</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $workOrder->location ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Due Date</dt>
                            <dd class="mt-1 text-sm font-semibold {{ $isOverdue ? 'text-red-700' : 'text-gray-900' }}">
                                {{ $due ? $due->format('Y-m-d') : '—' }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Description</dt>
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

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 bg-slate-50 flex justify-between">
                    <div>
                        <div class="text-xs uppercase font-semibold tracking-wide text-gray-500">Audit Trail</div>
                        <div class="mt-1 text-base font-semibold text-gray-900">Change History</div>
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $auditLogs->count() }} event{{ $auditLogs->count() === 1 ? '' : 's' }}
                    </div>
                </div>

                <div class="p-6">
                    <ol class="space-y-4">
                        @foreach($auditLogs as $log)
                            @php
                                $evt = $log->event ?? 'event';

                                $evtPill = match ($evt) {
                                    'created' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
                                    'updated' => 'bg-blue-50 text-blue-800 ring-blue-600/20',
                                    'status_changed' => 'bg-amber-50 text-amber-900 ring-amber-600/20',
                                    'deleted' => 'bg-red-50 text-red-800 ring-red-600/20',
                                    default => 'bg-gray-50 text-gray-800 ring-gray-600/20',
                                };

                                $props = is_array($log->properties ?? null) ? $log->properties : [];
                                $old = $props['old'] ?? [];
                                $new = $props['new'] ?? [];
                                $keys = array_values(array_diff(array_unique(array_merge(array_keys($old), array_keys($new))), $ignoreKeys));
                            @endphp

                            <li class="rounded-2xl border border-gray-200 p-4 bg-white hover:shadow-md transition">
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="inline-flex px-2 py-1 rounded-md font-semibold ring-1 ring-inset {{ $evtPill }}">
                                        {{ strtoupper(str_replace('_',' ',$evt)) }}
                                    </span>
                                    <span class="text-gray-500">{{ $log->created_at?->format('Y-m-d H:i:s') }}</span>
                                    <span class="text-gray-400">•</span>
                                    <span class="font-semibold text-gray-700">
                                        {{ $log->causer?->name ?? 'System' }}
                                    </span>
                                    @if($log->ip)
                                        <span class="text-gray-400">|</span>
                                        <span class="text-gray-600">IP {{ $log->ip }}</span>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    @if(empty($keys))
                                        <div class="text-sm text-gray-600">No field-level details recorded.</div>
                                    @else
                                        <div class="text-xs uppercase font-semibold text-gray-500">Key changes</div>
                                        <ul class="mt-2 space-y-1 text-sm">
                                            @foreach($keys as $k)
                                                <li>
                                                    <span class="font-semibold">{{ $humanKey($k) }}:</span>
                                                    <span class="text-gray-600">{{ $fmt($old[$k] ?? null) }}</span>
                                                    <span class="mx-1 text-gray-400 font-black">→</span>
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
