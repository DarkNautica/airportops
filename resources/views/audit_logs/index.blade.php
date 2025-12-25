<x-sidebar-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-900 leading-tight">Audit Logs</h2>
                <p class="text-sm text-gray-600 mt-1">Global timeline — search, filter, and click through to records.</p>
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('audit-logs.index') }}"
               class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50">
                Reset Filters
            </a>

            @if(\Illuminate\Support\Facades\Route::has('audit-logs.print'))
                <a href="{{ route('audit-logs.print', request()->query()) }}"
                   target="_blank"
                   class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50">
                    Print View
                </a>
            @endif

            @if(\Illuminate\Support\Facades\Route::has('audit-logs.export.csv'))
                <a href="{{ route('audit-logs.export.csv', request()->query()) }}"
                   class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50">
                    Export CSV
                </a>
            @endif

            @if(\Illuminate\Support\Facades\Route::has('audit-logs.export.pdf'))
                <a href="{{ route('audit-logs.export.pdf', request()->query()) }}"
                   class="inline-flex items-center px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                    Export PDF
                </a>
            @endif
        </div>
    </x-slot>

    @php
        $canSensitive = auth()->user()?->can('viewSensitive', \App\Models\AuditLog::class) ?? false;

        $evtPill = function($evt) {
            return match ($evt) {
                'created' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
                'updated' => 'bg-blue-50 text-blue-800 ring-blue-600/20',
                'status_changed' => 'bg-indigo-50 text-indigo-800 ring-indigo-600/20',
                'certified' => 'bg-emerald-50 text-emerald-800 ring-emerald-600/20',
                'unlocked' => 'bg-amber-50 text-amber-800 ring-amber-600/20',
                'deleted' => 'bg-red-50 text-red-800 ring-red-600/20',
                default => 'bg-gray-50 text-gray-800 ring-gray-600/20',
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
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 bg-slate-50">
                <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Filters</div>
            </div>

            <form method="GET" action="{{ route('audit-logs.index') }}" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-700">Search</label>
                        <input name="q" value="{{ $filters['q'] ?? '' }}"
                               placeholder="WO-000123, INSP-000001, title, etc."
                               class="mt-1 w-full rounded-xl border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm" />
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-700">Event</label>
                        <select name="event" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">All</option>
                            @foreach($eventOptions as $opt)
                                <option value="{{ $opt }}" @selected(($filters['event'] ?? '') === $opt)>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-700">Type</label>
                        <select name="type" class="mt-1 w-full rounded-xl border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm">
                            <option value="">All</option>
                            @foreach($typeOptions as $opt)
                                <option value="{{ $opt }}" @selected(($filters['type'] ?? '') === $opt)>{{ class_basename($opt) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-700">Causer ID</label>
                        <input name="causer_id" value="{{ $filters['causer_id'] ?? '' }}"
                               placeholder="2"
                               class="mt-1 w-full rounded-xl border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm" />
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-700">Date range</label>
                        <div class="mt-1 flex items-center gap-2">
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm" />
                            <span class="text-gray-400 text-sm">→</span>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                                   class="w-full rounded-xl border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm" />
                        </div>
                    </div>

                    @if($canSensitive)
                        <div class="md:col-span-2">
                            <label class="text-xs font-semibold text-gray-700">IP</label>
                            <input name="ip" value="{{ $filters['ip'] ?? '' }}"
                                   placeholder="127.0.0.1"
                                   class="mt-1 w-full rounded-xl border-gray-300 focus:border-gray-900 focus:ring-gray-900 text-sm" />
                        </div>
                    @endif
                </div>

                <div class="mt-5 flex items-center gap-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-xl bg-gray-900 text-white text-sm font-semibold hover:bg-black">
                        Apply
                    </button>
                    <a href="{{ route('audit-logs.index') }}"
                       class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-300 bg-white text-sm font-semibold text-gray-800 hover:bg-gray-50">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between bg-slate-50">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Timeline</div>
                    <div class="mt-1 text-base font-semibold text-gray-900">Audit Events</div>
                </div>
                <div class="text-xs text-gray-500">
                    Showing {{ $auditLogs->firstItem() ?? 0 }}–{{ $auditLogs->lastItem() ?? 0 }} of {{ $auditLogs->total() }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-white text-xs uppercase tracking-wide text-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Event</th>
                            <th class="px-4 py-3 text-left font-semibold">Subject</th>
                            <th class="px-4 py-3 text-left font-semibold">User</th>
                            <th class="px-4 py-3 text-left font-semibold">When</th>
                            @if($canSensitive)
                                <th class="px-4 py-3 text-left font-semibold">IP</th>
                            @endif
                            <th class="px-4 py-3 text-left font-semibold">Preview</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
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

                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {{ $evtPill($log->event) }}">
                                        {{ str($log->event)->replace('_',' ')->upper() }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-900">
                                        {{ $label }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        {{ $log->auditable_type ? class_basename($log->auditable_type) : '—' }} #{{ $log->auditable_id ?? '—' }}
                                        @if($href)
                                            <span class="mx-2 text-gray-300">|</span>
                                            <a href="{{ $href }}" class="text-blue-700 font-semibold hover:underline">Open</a>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">
                                        {{ $log->causer?->name ?? ($log->causer_id ? 'User #'.$log->causer_id : 'System') }}
                                    </div>
                                    <div class="text-xs text-gray-600">ID: {{ $log->causer_id ?? '—' }}</div>
                                </td>

                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                    {{ $log->created_at?->format('Y-m-d H:i:s') ?? '—' }}
                                </td>

                                @if($canSensitive)
                                    <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                        {{ $log->ip ?? '—' }}
                                    </td>
                                @endif

                                <td class="px-4 py-3 text-gray-700">
                                    @if($prev)
                                        <span class="text-gray-900 font-semibold">{{ $prev }}</span>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif

                                    <details class="mt-2">
                                        <summary class="cursor-pointer text-xs text-gray-600 hover:underline select-none">
                                            Details
                                        </summary>

                                        {{-- Pretty diff (human) --}}
                                        <div class="mt-2 rounded-xl border border-gray-200 bg-white overflow-hidden">
                                            <div class="px-3 py-2 bg-slate-50 border-b border-gray-200 text-xs font-semibold text-gray-700">
                                                Changes
                                            </div>

                                            @if(empty($diffKeys))
                                                <div class="px-3 py-3 text-xs text-gray-600">
                                                    No meaningful field-level changes recorded.
                                                </div>
                                            @else
                                                <div class="overflow-x-auto">
                                                    <table class="min-w-full text-xs">
                                                        <thead class="bg-white text-[11px] uppercase tracking-wide text-gray-500">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left font-semibold">Field</th>
                                                                <th class="px-3 py-2 text-left font-semibold">Old</th>
                                                                <th class="px-3 py-2 text-left font-semibold">New</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="divide-y divide-gray-200">
                                                            @foreach($diffKeys as $k)
                                                                @php
                                                                    $o = $normalize($old[$k] ?? null);
                                                                    $n = $normalize($new[$k] ?? null);
                                                                @endphp
                                                                <tr>
                                                                    <td class="px-3 py-2 font-semibold text-gray-900 whitespace-nowrap">
                                                                        {{ $humanKey($k) }}
                                                                    </td>
                                                                    <td class="px-3 py-2 text-gray-700">
                                                                        {{ $fmt($o) }}
                                                                    </td>
                                                                    <td class="px-3 py-2 text-gray-900 font-semibold">
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
                                            <div class="text-[11px] uppercase tracking-wide text-gray-500 font-semibold">Raw</div>
                                            <pre class="mt-2 rounded-xl bg-gray-50 border border-gray-200 p-3 text-xs overflow-auto whitespace-pre-wrap text-gray-800">{{ json_encode($log->properties ?? [], JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $canSensitive ? 6 : 5 }}" class="px-4 py-10 text-center text-gray-500">
                                    No audit logs found for these filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-white">
                {{ $auditLogs->links() }}
            </div>
        </div>

    </div>
</x-sidebar-app-layout>
