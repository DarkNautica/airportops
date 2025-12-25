@props([
    'logs' => collect(),
])

@php
    $badge = function(string $event) {
        return match ($event) {
            'created'        => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
            'updated'        => 'bg-blue-50 text-blue-800 ring-blue-600/20',
            'status_changed' => 'bg-amber-50 text-amber-900 ring-amber-600/20',
            'deleted'        => 'bg-red-50 text-red-800 ring-red-600/20',
            'certified'      => 'bg-indigo-50 text-indigo-800 ring-indigo-600/20',
            'unlocked'       => 'bg-gray-50 text-gray-800 ring-gray-600/20',
            default          => 'bg-gray-50 text-gray-800 ring-gray-600/20',
        };
    };

    $label = function(string $event) {
        return str_replace('_', ' ', ucfirst($event));
    };

    $humanKey = function(string $k) {
        return str($k)->replace('_', ' ')->headline();
    };

    // Ignore noisy fields in the UI
    $ignoreKeys = ['updated_at', 'created_at'];
@endphp

<div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-slate-50">
        <div>
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-600">Audit Trail</div>
            <div class="mt-1 font-bold text-gray-900">Change Timeline</div>
        </div>
        <div class="text-xs text-gray-500">
            {{ $logs->count() }} event{{ $logs->count() === 1 ? '' : 's' }}
        </div>
    </div>

    <div class="p-6">
        @if($logs->isEmpty())
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-700">
                No audit history found.
            </div>
        @else
            <ol class="space-y-4">
                @foreach($logs as $log)
                    @php
                        $event = $log->event ?? '—';
                        $who = $log->causer?->name
                            ?? ($log->user?->name ?? null)
                            ?? ($log->causer_id ? ('User #'.$log->causer_id) : 'System');

                        $at = $log->created_at?->format('Y-m-d H:i:s') ?? '—';

                        $props = is_array($log->properties ?? null) ? $log->properties : [];
                        $old = is_array($props['old'] ?? null) ? $props['old'] : [];
                        $new = is_array($props['new'] ?? null) ? $props['new'] : [];

                        // For WorkOrderObserver you store "new" as dirty-only on updates.
                        // On created you store full snapshot in new.
                        // On deleted you store old snapshot in old.

                        // Build a key-change list: prefer keys from $new; fallback to $old.
                        $keys = array_unique(array_merge(array_keys($new), array_keys($old)));
                        $keys = array_values(array_filter($keys, fn($k) => !in_array($k, $ignoreKeys, true)));

                        // Keep it readable — don’t spam the UI
                        $maxLines = 10;
                        $keysLimited = array_slice($keys, 0, $maxLines);
                        $extraCount = max(0, count($keys) - count($keysLimited));

                        $fmt = function($v) {
                            if (is_null($v)) return '—';
                            if (is_bool($v)) return $v ? 'true' : 'false';
                            if (is_array($v)) return json_encode($v);
                            return (string) $v;
                        };
                    @endphp

                    <li class="rounded-2xl border border-gray-200 bg-white p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $badge($event) }}">
                                        {{ $label($event) }}
                                    </span>

                                    <span class="text-xs text-gray-500">
                                        {{ $at }}
                                    </span>

                                    <span class="text-xs text-gray-400">
                                        •
                                    </span>

                                    <span class="text-xs font-semibold text-gray-700">
                                        {{ $who }}
                                    </span>
                                </div>

                                {{-- Key changes --}}
                                <div class="mt-3">
                                    @if(empty($keys))
                                        <div class="text-sm text-gray-600">
                                            No field-level details recorded.
                                        </div>
                                    @else
                                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                            Key changes
                                        </div>

                                        <ul class="mt-2 space-y-1 text-sm">
                                            @foreach($keysLimited as $k)
                                                <li class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                                    <span class="font-semibold text-gray-900">
                                                        {{ $humanKey($k) }}:
                                                    </span>

                                                    <span class="text-gray-600">
                                                        {{ $fmt($old[$k] ?? null) }}
                                                    </span>

                                                    <span class="text-gray-400 font-black">→</span>

                                                    <span class="text-gray-900">
                                                        {{ $fmt($new[$k] ?? null) }}
                                                    </span>
                                                </li>
                                            @endforeach

                                            @if($extraCount > 0)
                                                <li class="text-xs text-gray-500 pt-1">
                                                    + {{ $extraCount }} more change{{ $extraCount === 1 ? '' : 's' }}…
                                                </li>
                                            @endif
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            {{-- Context --}}
                            <div class="shrink-0 text-right">
                                <div class="text-xs text-gray-500">IP</div>
                                <div class="text-xs font-semibold text-gray-800">{{ $log->ip ?? '—' }}</div>

                                <details class="mt-2">
                                    <summary class="cursor-pointer text-xs font-semibold text-gray-600 hover:text-gray-900">
                                        Details
                                    </summary>
                                    <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-800 font-mono whitespace-pre-wrap">
                                        {{ json_encode($log->properties ?? [], JSON_PRETTY_PRINT) }}
                                    </div>
                                </details>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</div>
