<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Audit Logs</h1>
        <p class="text-sm text-gray-500 mt-1">Global timeline — search, filter, and click through to records.</p>
    </x-slot>

    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('audit-logs.index') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg border border-surface-border bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                Reset Filters
            </a>

            @if(\Illuminate\Support\Facades\Route::has('audit-logs.print'))
                <a href="{{ route('audit-logs.print', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-surface-border bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    Print View
                </a>
            @endif

            @if(\Illuminate\Support\Facades\Route::has('audit-logs.export.csv'))
                <a href="{{ route('audit-logs.export.csv', request()->query()) }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-surface-border bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                    Export CSV
                </a>
            @endif

            @if(\Illuminate\Support\Facades\Route::has('audit-logs.export.pdf'))
                <a href="{{ route('audit-logs.export.pdf', request()->query()) }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black transition">
                    Export PDF
                </a>
            @endif
        </div>
    </x-slot>

    @php
        $canSensitive = auth()->user()?->can('viewSensitive', \App\Models\AuditLog::class) ?? false;

        $evtBadge = function($evt) {
            return match ($evt) {
                'created' => 'green',
                'updated' => 'open',
                'status_changed' => 'in_progress',
                'certified' => 'certified',
                'deleted' => 'critical',
                'unlocked' => 'neutral',
                'submitted' => 'green',
                default => 'neutral',
            };
        };

        $evtDot = function($evt) {
            return match ($evt) {
                'created', 'submitted', 'certified' => 'green',
                'updated' => 'blue',
                'status_changed' => 'amber',
                'deleted' => 'red',
                'unlocked' => 'neutral',
                default => 'neutral',
            };
        };

        $subjectLink = function($log) {
            $type = $log->auditable_type;
            $id   = $log->auditable_id;

            $map = [
                \App\Models\WorkOrder::class => 'work-orders.show',
                \App\Models\Inspection::class => 'inspections.show',
            ];

            if (!$type || !$id) return null;
            if (!isset($map[$type])) return null;
            if (!\Illuminate\Support\Facades\Route::has($map[$type])) return null;

            return route($map[$type], $id);
        };

        $subjectLabel = function($log) {
            return match ($log->auditable_type) {
                \App\Models\WorkOrder::class => 'Work Order',
                \App\Models\Inspection::class => 'Inspection',
                default => class_basename((string)$log->auditable_type ?: '—'),
            };
        };

        $preview = function($props) {
            $props = is_array($props) ? $props : [];
            $new = is_array($props['new'] ?? null) ? $props['new'] : [];
            $old = is_array($props['old'] ?? null) ? $props['old'] : [];

            foreach (['wo_number','insp_number','title'] as $k) {
                if (isset($new[$k])) return $new[$k];
                if (isset($old[$k])) return $old[$k];
            }

            if (!empty($props['dirty']) && is_array($props['dirty'])) {
                return 'Changed: ' . implode(', ', array_slice($props['dirty'], 0, 3));
            }

            return null;
        };

        // ============================
        // Pretty diff helpers (DETAILS)
        // ============================
        $humanKey = fn(string $k) => str($k)->replace('_', ' ')->headline();

        $normalize = function ($v) {
            if (is_null($v)) return null;
            if (is_bool($v)) return $v ? 'true' : 'false';
            if (is_array($v)) return '[json]';

            if (is_string($v)) {
                $s = trim($v);
                if (preg_match('/^\d{4}-\d{2}-\d{2}T/', $s)) return substr($s, 0, 10);
                return $s === '' ? null : $s;
            }

            return (string) $v;
        };

        $fmt = fn($v) => ($v === null || $v === '') ? '—' : $v;

        $ignoreDiffKeys = ['id','created_at','updated_at','auditable_type','auditable_id','properties'];
        $skipDiffKeys = ['header','checklist'];
    @endphp

    <div class="space-y-6">

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
            <div class="panel-header">
                <span class="panel-header-label">FILTERS</span>
            </div>

            <form method="GET" action="{{ route('audit-logs.index') }}" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input name="q" value="{{ $filters['q'] ?? '' }}"
                               placeholder="WO-000123, INSP-000001, title, etc."
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Event</label>
                        <select name="event" class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="">All</option>
                            @foreach($eventOptions as $opt)
                                <option value="{{ $opt }}" @selected(($filters['event'] ?? '') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                            <option value="">All</option>
                            @foreach($typeOptions as $opt)
                                <option value="{{ $opt }}" @selected(($filters['type'] ?? '') === $opt)>{{ class_basename($opt) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Causer ID</label>
                        <input name="causer_id" value="{{ $filters['causer_id'] ?? '' }}"
                               placeholder="2"
                               class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date range</label>
                        <div class="flex items-center gap-2">
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                            <span class="text-gray-400 text-sm">&rarr;</span>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        </div>
                    </div>

                    @if($canSensitive)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">IP</label>
                            <input name="ip" value="{{ $filters['ip'] ?? '' }}"
                                   placeholder="127.0.0.1"
                                   class="w-full rounded-lg border border-surface-border bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" />
                        </div>
                    @endif
                </div>

                <div class="mt-5 flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2.5 rounded-lg bg-gray-900 text-white text-sm font-medium hover:bg-black transition">
                        Apply
                    </button>
                    <a href="{{ route('audit-logs.index') }}"
                       class="inline-flex items-center px-4 py-2.5 rounded-lg border border-surface-border bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
            <div class="panel-header flex items-center justify-between">
                <div>
                    <span class="panel-header-label">TIMELINE</span>
                    <div class="mt-1 text-base font-semibold text-gray-900">Audit Events</div>
                </div>
                <div class="text-xs text-gray-500">
                    Showing {{ $auditLogs->firstItem() ?? 0 }}&ndash;{{ $auditLogs->lastItem() ?? 0 }} of {{ $auditLogs->total() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-surface-border">
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Event</th>
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Subject</th>
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">User</th>
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">When</th>
                            @if($canSensitive)
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">IP</th>
                            @endif
                            <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Preview</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-surface-border">
                        @forelse($auditLogs as $log)
                            @php
                                $href = $subjectLink($log);
                                $label = $subjectLabel($log);
                                $prev = $preview($log->properties);

                                $props = is_array($log->properties ?? null) ? $log->properties : [];
                                $old = is_array($props['old'] ?? null) ? $props['old'] : [];
                                $new = is_array($props['new'] ?? null) ? $props['new'] : [];
                                $dirty = (!empty($props['dirty']) && is_array($props['dirty'])) ? $props['dirty'] : array_unique(array_merge(array_keys($old), array_keys($new)));

                                $diffKeys = [];
                                foreach ($dirty as $k) {
                                    if (!is_string($k)) continue;
                                    if (in_array($k, $ignoreDiffKeys, true)) continue;
                                    if (in_array($k, $skipDiffKeys, true)) continue;

                                    $o = $normalize($old[$k] ?? null);
                                    $n = $normalize($new[$k] ?? null);
                                    if ($o === $n) continue;

                                    $diffKeys[] = $k;
                                }
                                $diffKeys = array_slice($diffKeys, 0, 30);
                            @endphp

                            <tr class="hover:bg-[#F8FAFC]">
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <x-badge variant="{{ $evtBadge($log->event) }}">
                                        {{ str($log->event)->replace('_',' ')->upper() }}
                                    </x-badge>
                                </td>

                                <td class="px-4 py-2.5">
                                    <div class="font-semibold text-sm text-gray-900">
                                        {{ $label }}
                                    </div>
                                    <div class="font-mono text-[11px] text-slate-500">
                                        {{ $log->auditable_type ? class_basename($log->auditable_type) : '—' }} #{{ $log->auditable_id ?? '—' }}
                                        @if($href)
                                            <span class="mx-1.5 text-gray-300">|</span>
                                            <a href="{{ $href }}" class="text-[#2563EB] hover:text-[#3B82F6] font-semibold">Open</a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $log->causer?->name ?? ($log->causer_id ? 'User #'.$log->causer_id : 'System') }}
                                    </div>
                                    <div class="font-mono text-[11px] text-slate-500">ID: {{ $log->causer_id ?? '—' }}</div>
                                </td>

                                <td class="px-4 py-2.5 whitespace-nowrap font-mono text-[11px] text-gray-700">
                                    {{ $log->created_at?->format('Y-m-d H:i:s') ?? '—' }}
                                </td>

                                @if($canSensitive)
                                    <td class="px-4 py-2.5 whitespace-nowrap font-mono text-[11px] text-gray-700">
                                        {{ $log->ip ?? '—' }}
                                    </td>
                                @endif

                                <td class="px-4 py-2.5 text-sm text-gray-700">
                                    @if($prev)
                                        <span class="text-gray-900 font-medium">{{ $prev }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif

                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-xs text-[#2563EB] hover:text-[#3B82F6] select-none font-medium">
                                            Details
                                        </summary>

                                        {{-- Pretty diff (human) --}}
                                        <div class="mt-2 rounded-lg border border-surface-border bg-white overflow-hidden">
                                            <div class="px-3 py-2 bg-slate-50 border-b border-surface-border">
                                                <span class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">CHANGES</span>
                                            </div>

                                            @if(empty($diffKeys))
                                                <div class="px-3 py-3 text-xs text-gray-500">
                                                    No meaningful field-level changes recorded.
                                                </div>
                                            @else
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full text-xs">
                                                        <thead>
                                                            <tr class="border-b border-surface-border">
                                                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-3 py-2 text-left">Field</th>
                                                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-3 py-2 text-left">Old</th>
                                                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-3 py-2 text-left">New</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-surface-border">
                                                            @foreach($diffKeys as $k)
                                                                @php
                                                                    $o = $normalize($old[$k] ?? null);
                                                                    $n = $normalize($new[$k] ?? null);
                                                                @endphp
                                                                <tr class="hover:bg-[#F8FAFC]">
                                                                    <td class="px-3 py-2 font-medium text-gray-900 whitespace-nowrap text-xs">
                                                                        {{ $humanKey($k) }}
                                                                    </td>
                                                                    <td class="px-3 py-2 text-gray-500 text-xs">
                                                                        {{ $fmt($o) }}
                                                                    </td>
                                                                    <td class="px-3 py-2 text-gray-900 font-medium text-xs">
                                                                        {{ $fmt($n) }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Raw details (still available, but secondary) --}}
                                        <div class="mt-3">
                                            <div class="font-mono text-[10px] uppercase tracking-widest text-slate-500 font-semibold">RAW</div>
                                            <pre class="mt-2 rounded-lg bg-slate-50 border border-surface-border p-3 text-xs overflow-auto whitespace-pre-wrap text-gray-700 font-mono">{{ json_encode($log->properties ?? [], JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canSensitive ? 6 : 5 }}" class="px-4 py-10 text-center text-gray-500 text-sm">
                                    No audit logs found for these filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-surface-border bg-white">
                {{ $auditLogs->links() }}
            </div>
        </div>

    </div>
</x-sidebar-app-layout>
