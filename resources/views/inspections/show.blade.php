{{-- resources/views/inspections/show.blade.php --}}
<x-sidebar-app-layout>
    <x-slot name="header">
        <h1 class="font-instrument text-xl text-gray-900">Inspection {{ $inspection->insp_number }}</h1>
    </x-slot>

    <x-slot name="actions">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('inspections.index') }}"
               class="inline-flex items-center gap-1 px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50 shadow-sm">
                &larr; Back
            </a>

            <a href="{{ route('inspections.print', $inspection) }}"
               target="_blank"
               class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50 shadow-sm">
                Print
            </a>

            <a href="{{ route('inspections.pdf', $inspection) }}"
               class="inline-flex items-center px-3 py-2 rounded-md border border-gray-300 bg-white text-gray-800 text-sm font-semibold hover:bg-gray-50 shadow-sm">
                PDF
            </a>

            @can('update', $inspection)
                <a href="{{ route('inspections.edit', $inspection) }}"
                   class="inline-flex items-center px-3 py-2 rounded-md bg-gray-900 text-white text-sm font-semibold hover:bg-black shadow-sm">
                    Edit
                </a>
            @endcan

            @can('delete', $inspection)
                <form method="POST"
                      action="{{ route('inspections.destroy', $inspection) }}"
                      onsubmit="return confirm('Delete this inspection record? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center px-3 py-2 rounded-md border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 shadow-sm">
                        Delete
                    </button>
                </form>
            @endcan
        </div>
    </x-slot>

    @php
        $header = is_array($inspection->header ?? null) ? $inspection->header : [];
        $checklist = is_array($inspection->checklist ?? null) ? $inspection->checklist : [];

        $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : '—';
        $fmtTime = fn($t) => $t ? \Carbon\Carbon::parse($t)->format('H:i') : '—';
        $val = fn($v) => filled($v) ? $v : '—';

        $locked = (bool) ($inspection->is_locked ?? false);

        $labels = [
            'pavement_lip_over_3' => 'Pavement lip over 3"',
            'holes_over_5_dia' => 'Holes > 5" dia, > 3" deep',
            'cracks_spalling_bumps' => 'Cracks / spalling / bumps',
            'fod' => 'FOD (gravel, debris, etc.)',
            'rubber_deposits' => 'Rubber deposits',
            'ponding_edge_dams' => 'Ponding / edge dams',
            'ruts_humps_erosion' => 'Ruts / humps / erosion',
            'drainage_construction' => 'Drainage / construction',
            'objects_frangible_base' => 'Objects / frangible base',
            'visibility_standard' => 'Visibility standard',
            'hold_lines_signs' => 'Hold lines / signs',
            'frangible_signs' => 'Frangible signs',
            'lighting_obscured_dirty_fading' => 'Obscured / dirty / fading',
            'lighting_damaged_missing' => 'Damaged / missing',
            'lighting_inoperative' => 'Inoperative',
            'lighting_faulty_aim' => 'Faulty aim / adjustment',
            'rotating_beacon' => 'Rotating beacon',
            'wind_indicators' => 'Wind indicators',
            'reils_papi_ils' => 'REILs / PAPI / ILS systems',
            'obstruction_lights' => 'Obstruction lights',
            'cranes_trees' => 'Cranes / trees',
            'wildlife_present_location' => 'Wildlife present / location',
            'complying_whmp' => 'Complying with WHMP',
            'fuel_fencing_gates_signs' => 'Fencing / gates / signs',
            'fuel_marking_labeling' => 'Fuel marking / labeling',
            'fuel_extinguishers_ground_clips' => 'Fire exting. / ground clips',
            'fuel_leaks_vegetation' => 'Fuel leaks / vegetation',
            'snow_surface_conditions' => 'Surface conditions',
            'snow_bank_clearance' => 'Snow bank clearance',
            'snow_lights_signs_obscured' => 'Lights / signs obscured',
            'snow_navaids_fire_access' => 'Navaids / fire access',
            'arff_equipment_crew_availability' => 'Equipment / crew availability',
            'arff_response_routes_clear' => 'Response routes clear',
            'public_fencing_gates_signs' => 'Fencing / gates / signs',
            'public_unauthorized_persons_vehicles' => 'Unauthorized persons / veh.',
            'construction_barricades_lights' => 'Barricades / lights',
            'construction_equipment_parking' => 'Equipment parking',
        ];

        $amCounts = ['S' => 0, 'U' => 0, 'NA' => 0];
        $pmCounts = ['S' => 0, 'U' => 0, 'NA' => 0];

        foreach ($checklist as $row) {
            $am = strtoupper((string)($row['am'] ?? ''));
            $pm = strtoupper((string)($row['pm'] ?? ''));
            if (isset($amCounts[$am])) $amCounts[$am]++;
            if (isset($pmCounts[$pm])) $pmCounts[$pm]++;
        }

        $overall = $header['overall_status'] ?? null;
        $overallVariant = match ($overall) {
            'Satisfactory' => 'satisfactory',
            'Unsatisfactory' => 'unsatisfactory',
            default => 'neutral',
        };

        $checkBadgeVariant = function ($v) {
            return match ($v) {
                'S' => 'satisfactory',
                'U' => 'unsatisfactory',
                'NA' => 'neutral',
                default => 'neutral',
            };
        };
    @endphp

    <div class="min-h-screen bg-surface pb-10">
        <div class="w-full px-6 lg:px-10 py-8 space-y-6">

            {{-- FLASH BANNERS --}}
            @if (session('success'))
                <div class="bg-white rounded-xl shadow-card overflow-hidden border-l-4 border-l-emerald-400">
                    <div class="px-5 py-4 flex items-start gap-3">
                        <x-status-dot color="green" :pulse="true" size="md" class="mt-1" />
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Success</div>
                            <div class="text-sm text-gray-600 mt-0.5">{{ session('success') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-white rounded-xl shadow-card overflow-hidden border-l-4 border-l-red-400">
                    <div class="px-5 py-4 flex items-start gap-3">
                        <x-status-dot color="red" :pulse="true" size="md" class="mt-1" />
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Blocked</div>
                            <div class="text-sm text-gray-600 mt-0.5">{{ session('error') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- LOCK STATE & CERTIFICATION STRIP --}}
            <div class="c139-card">
                <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Lock State:</span>
                        @if($locked)
                            <x-badge variant="locked">Locked</x-badge>
                        @else
                            <x-badge variant="open">Open</x-badge>
                        @endif

                        @if($locked)
                            <span class="font-mono text-[11px] text-slate-500">
                                Locked {{ $inspection->locked_at?->format('m/d/Y H:i') ?? '—' }}
                                by {{ optional($inspection->lockedBy)->name ?? '—' }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @can('certify', $inspection)
                            @if(!$locked)
                                <form method="POST" action="{{ route('inspections.certify', $inspection) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm transition"
                                            onclick="return confirm('Certify and lock this inspection?');">
                                        Certify & Lock
                                    </button>
                                </form>
                            @endif
                        @endcan

                        @can('unlock', $inspection)
                            @if($locked)
                                <form method="POST" action="{{ route('inspections.unlock', $inspection) }}">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-2 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 shadow-sm transition"
                                            onclick="return confirm('Unlock this inspection?');">
                                        Unlock
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </div>
                </div>
            </div>

            {{-- SUMMARY CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] bg-[#2563EB]"></div>
                    <div class="p-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Date</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $fmtDate($inspection->inspection_date) }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $val($header['inspection_day'] ?? null) }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] {{ $overall === 'Satisfactory' ? 'bg-[#16A34A]' : ($overall === 'Unsatisfactory' ? 'bg-[#DC2626]' : 'bg-slate-300') }}"></div>
                    <div class="p-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Overall</div>
                        <div class="mt-2">
                            <x-badge :variant="$overallVariant">{{ $val($overall) }}</x-badge>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] {{ $locked ? 'bg-slate-400' : 'bg-[#2563EB]' }}"></div>
                    <div class="p-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Status</div>
                        <div class="mt-2">
                            @if($locked)
                                <x-badge variant="certified">Certified</x-badge>
                            @else
                                <x-badge variant="open">Editable</x-badge>
                            @endif
                        </div>
                        @if($locked)
                            <div class="mt-2 font-mono text-[11px] text-slate-500">
                                {{ $inspection->certified_at?->format('m/d/Y H:i') ?? '—' }}
                                by {{ optional($inspection->certifiedBy)->name ?? '—' }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-card border border-surface-border overflow-hidden">
                    <div class="h-[2px] bg-[#8B5CF6]"></div>
                    <div class="p-5">
                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Inspector</div>
                        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $inspection->inspector?->name ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- TIMES & CHECKLIST SNAPSHOT --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Times --}}
                <div class="c139-card">
                    <div class="panel-header">
                        <span class="panel-header-label">Inspection Times</span>
                    </div>

                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-lg border border-surface-border bg-[#F8FAFC] p-4">
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Crash Phone Test</div>
                            <div class="mt-1 font-mono text-[11px] font-semibold text-gray-900">{{ $fmtTime($header['crash_phone_test_time'] ?? null) }}</div>
                            <div class="mt-1 text-xs text-slate-500">By: {{ $val($header['by_1'] ?? null) }}</div>
                        </div>

                        <div class="rounded-lg border border-surface-border bg-[#F8FAFC] p-4">
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">AM Inspection</div>
                            <div class="mt-1 font-mono text-[11px] font-semibold text-gray-900">{{ $fmtTime($header['am_time'] ?? null) }}</div>
                            <div class="mt-1 text-xs text-slate-500">By: {{ $val($header['by_2'] ?? null) }}</div>
                        </div>

                        <div class="rounded-lg border border-surface-border bg-[#F8FAFC] p-4">
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">PM Inspection</div>
                            <div class="mt-1 font-mono text-[11px] font-semibold text-gray-900">{{ $fmtTime($header['pm_time'] ?? null) }}</div>
                            <div class="mt-1 text-xs text-slate-500">By: {{ $val($header['by_3'] ?? null) }}</div>
                        </div>

                        <div class="rounded-lg border border-surface-border bg-[#F8FAFC] p-4">
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500">Other Inspection</div>
                            <div class="mt-1 font-mono text-[11px] font-semibold text-gray-900">{{ $fmtTime($header['other_time'] ?? null) }}</div>
                            <div class="mt-1 text-xs text-slate-500">By: {{ $val($header['by_4'] ?? null) }}</div>
                        </div>
                    </div>
                </div>

                {{-- Checklist Snapshot --}}
                <div class="c139-card">
                    <div class="panel-header">
                        <span class="panel-header-label">Checklist Snapshot</span>
                    </div>

                    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-lg border border-surface-border p-4">
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-3">AM Counts</div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Satisfactory</span>
                                    <span class="font-mono text-[11px] font-semibold text-emerald-700">{{ $amCounts['S'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Unsatisfactory</span>
                                    <span class="font-mono text-[11px] font-semibold text-red-700">{{ $amCounts['U'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">N/A</span>
                                    <span class="font-mono text-[11px] font-semibold text-slate-600">{{ $amCounts['NA'] }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-lg border border-surface-border p-4">
                            <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-3">PM Counts</div>
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Satisfactory</span>
                                    <span class="font-mono text-[11px] font-semibold text-emerald-700">{{ $pmCounts['S'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">Unsatisfactory</span>
                                    <span class="font-mono text-[11px] font-semibold text-red-700">{{ $pmCounts['U'] }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">N/A</span>
                                    <span class="font-mono text-[11px] font-semibold text-slate-600">{{ $pmCounts['NA'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-5 pb-4 font-mono text-[10px] text-slate-400">
                        Counts based on saved checklist JSON values.
                    </div>
                </div>
            </div>

            {{-- CHECKLIST TABLE --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">Checklist</span>
                    <span class="font-mono text-[10px] text-slate-500">AM / PM: S / U / N/A</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="c139-table w-full">
                        <thead>
                            <tr>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Item</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-center w-24">AM</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-center w-24">PM</th>
                                <th class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 px-4 py-3 text-left">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-border">
                            @forelse($checklist as $key => $row)
                                @php
                                    $am = strtoupper((string)($row['am'] ?? ''));
                                    $pm = strtoupper((string)($row['pm'] ?? ''));
                                    $rm = trim((string)($row['remarks'] ?? ''));

                                    $label = $labels[$key] ?? $key;
                                @endphp

                                <tr class="h-11 hover:bg-[#F8FAFC] transition-colors">
                                    <td class="px-4 py-2.5 text-sm text-gray-900 font-medium">
                                        {{ $label }}
                                        @if(!isset($labels[$key]))
                                            <div class="font-mono text-[10px] text-slate-400 font-normal">{{ $key }}</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2.5 text-center">
                                        @if($am)
                                            <x-badge :variant="$checkBadgeVariant($am)" :dot="false">{{ $am === 'NA' ? 'N/A' : $am }}</x-badge>
                                        @else
                                            <span class="text-slate-400 text-xs">—</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2.5 text-center">
                                        @if($pm)
                                            <x-badge :variant="$checkBadgeVariant($pm)" :dot="false">{{ $pm === 'NA' ? 'N/A' : $pm }}</x-badge>
                                        @else
                                            <span class="text-slate-400 text-xs">—</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2.5 text-sm text-gray-800">{{ $rm }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-16 text-center text-slate-400 text-sm">
                                        No checklist data recorded.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FINDINGS --}}
            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">Findings</span>
                </div>
                <div class="p-5">
                    <div class="whitespace-pre-wrap text-sm text-gray-800">{{ $inspection->findings ?? '—' }}</div>
                </div>
            </div>

            {{-- AUDIT TRAIL --}}
            @php
                $ignoreKeys = ['id','created_at','updated_at','auditable_type','auditable_id'];
                $skipKeys   = ['header','checklist','properties'];

                $allowKeys = [
                    'status', 'is_locked', 'locked_at', 'locked_by', 'certified_at',
                    'insp_number', 'inspection_date', 'inspection_time', 'inspection_type',
                    'surface', 'conditions', 'findings',
                ];

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
            @endphp

            <div class="c139-card">
                <div class="panel-header">
                    <span class="panel-header-label">Audit Trail</span>
                    <span class="font-mono text-[10px] text-slate-500">
                        {{ $auditLogs->count() }} event{{ $auditLogs->count() === 1 ? '' : 's' }}
                    </span>
                </div>

                <div class="p-5">
                    <ol class="space-y-4">
                        @foreach($auditLogs as $log)
                            @php
                                $evt = $log->event ?? 'event';

                                $evtVariant = match ($evt) {
                                    'created' => 'certified',
                                    'updated', 'status_changed' => 'open',
                                    'certified' => 'active',
                                    'unlocked' => 'neutral',
                                    'deleted' => 'critical',
                                    default => 'neutral',
                                };

                                $props = is_array($log->properties ?? null) ? $log->properties : [];
                                $old = $props['old'] ?? [];
                                $new = $props['new'] ?? [];

                                $changes = [];
                                foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $k) {
                                    if (in_array($k, $ignoreKeys, true)) continue;
                                    if (in_array($k, $skipKeys, true)) continue;
                                    if (!in_array($k, $allowKeys, true)) continue;

                                    $o = $normalize($old[$k] ?? null);
                                    $n = $normalize($new[$k] ?? null);
                                    if ($o === $n) continue;

                                    $changes[$k] = ['old' => $o, 'new' => $n];
                                }
                            @endphp

                            <li class="rounded-xl border border-surface-border p-4 bg-white hover:shadow-md transition">
                                <div class="flex flex-wrap items-center gap-2">
                                    <x-badge :variant="$evtVariant">{{ strtoupper(str_replace('_',' ',$evt)) }}</x-badge>
                                    <span class="font-mono text-[11px] text-slate-500">{{ $log->created_at?->format('Y-m-d H:i:s') }}</span>
                                    <span class="text-slate-300">&middot;</span>
                                    <span class="text-sm font-semibold text-gray-700">
                                        {{ $log->causer?->name ?? 'System' }}
                                    </span>
                                    @if($log->ip)
                                        <span class="text-slate-300">|</span>
                                        <span class="font-mono text-[11px] text-slate-500">IP {{ $log->ip }}</span>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    @if(empty($changes))
                                        <div class="text-sm text-slate-400">No meaningful field changes recorded.</div>
                                    @else
                                        <div class="font-mono text-[10px] font-semibold uppercase tracking-widest text-slate-500 mb-2">Key Changes</div>
                                        <ul class="space-y-1 text-sm">
                                            @foreach($changes as $k => $chg)
                                                <li>
                                                    <span class="font-semibold text-gray-900">{{ $humanKey($k) }}:</span>
                                                    <span class="text-slate-500">{{ $fmt($chg['old']) }}</span>
                                                    <span class="mx-1 text-slate-300 font-black">&rarr;</span>
                                                    <span class="text-gray-900">{{ $fmt($chg['new']) }}</span>
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
