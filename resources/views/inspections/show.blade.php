{{-- resources/views/inspections/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-xs text-gray-500">Part 139 Daily Safety Self-Inspection</div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Inspection {{ $inspection->insp_number }}
                </h2>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('inspections.index') }}"
                   class="px-3 py-1.5 rounded border text-sm text-gray-700 hover:bg-gray-50">
                    ← Back
                </a>

                <a href="{{ route('inspections.print', $inspection) }}"
                   target="_blank"
                   class="px-3 py-1.5 rounded border text-sm text-gray-700 hover:bg-gray-50">
                    Print
                </a>

                <a href="{{ route('inspections.pdf', $inspection) }}"
                   class="px-3 py-1.5 rounded border text-sm text-gray-700 hover:bg-gray-50">
                    PDF
                </a>

                @can('update', $inspection)
                    <a href="{{ route('inspections.edit', $inspection) }}"
                       class="px-3 py-1.5 rounded bg-indigo-600 text-white text-sm hover:bg-indigo-700">
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
                                class="px-3 py-1.5 rounded bg-red-600 text-white text-sm hover:bg-red-700">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    @php
        $header = is_array($inspection->header ?? null) ? $inspection->header : [];
        $checklist = is_array($inspection->checklist ?? null) ? $inspection->checklist : [];

        $fmtDate = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : '—';
        $fmtTime = fn($t) => $t ? \Carbon\Carbon::parse($t)->format('H:i') : '—';
        $val = fn($v) => filled($v) ? $v : '—';

        $locked = (bool) ($inspection->is_locked ?? false);

        // If you want pretty labels in the show view, keep this list matching create/print.
        // If a key isn't found here, the table will fall back to showing the raw key.
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

        // Checklist stats (S/U/NA)
        $amCounts = ['S' => 0, 'U' => 0, 'NA' => 0];
        $pmCounts = ['S' => 0, 'U' => 0, 'NA' => 0];

        foreach ($checklist as $row) {
            $am = strtoupper((string)($row['am'] ?? ''));
            $pm = strtoupper((string)($row['pm'] ?? ''));
            if (isset($amCounts[$am])) $amCounts[$am]++;
            if (isset($pmCounts[$pm])) $pmCounts[$pm]++;
        }

        $overall = $header['overall_status'] ?? null;
        $overallBadge = match ($overall) {
            'Satisfactory' => 'bg-green-100 text-green-800 border-green-200',
            'Unsatisfactory' => 'bg-red-100 text-red-800 border-red-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };

        $lockBadge = $locked
            ? 'bg-green-100 text-green-800 border-green-200'
            : 'bg-yellow-100 text-yellow-800 border-yellow-200';

        $badge = function ($v) {
            return match ($v) {
                'S' => 'bg-green-50 text-green-800 border-green-200',
                'U' => 'bg-red-50 text-red-800 border-red-200',
                'NA' => 'bg-gray-50 text-gray-800 border-gray-200',
                default => 'bg-white text-gray-500 border-gray-200',
            };
        };
    @endphp

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- FLASH BANNERS --}}
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                    <span class="font-semibold">Success:</span> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    <span class="font-semibold">Blocked:</span> {{ session('error') }}
                </div>
            @endif

            {{-- COMMAND BAR --}}
            <div class="bg-white border rounded-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-gray-700">
                    <span class="font-semibold">Lock State:</span>
                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded border text-xs font-semibold {{ $lockBadge }}">
                        {{ $locked ? 'LOCKED' : 'OPEN' }}
                    </span>

                    @if($locked)
                        <span class="ml-3 text-xs text-gray-500">
                            Locked at {{ $inspection->locked_at?->format('m/d/Y H:i') ?? '—' }}
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
                                        class="px-3 py-1.5 rounded bg-green-600 text-white text-sm hover:bg-green-700"
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
                                        class="px-3 py-1.5 rounded bg-red-600 text-white text-sm hover:bg-red-700"
                                        onclick="return confirm('Unlock this inspection?');">
                                    Unlock
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>

            {{-- SUMMARY STRIP --}}
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="p-4 grid grid-cols-1 sm:grid-cols-4 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">Date</div>
                        <div class="font-semibold text-gray-900">{{ $fmtDate($inspection->inspection_date) }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Overall</div>
                        <div class="inline-flex items-center px-2 py-1 rounded border text-xs font-semibold {{ $overallBadge }}">
                            {{ $val($overall) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Status</div>
                        <div class="inline-flex items-center px-2 py-1 rounded border text-xs font-semibold {{ $lockBadge }}">
                            {{ $locked ? 'Locked (Certified)' : 'Open (Editable)' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Inspector</div>
                        <div class="font-semibold text-gray-900">{{ $inspection->inspector?->name ?? '—' }}</div>
                    </div>
                </div>

                @if($locked)
                    <div class="px-4 pb-4 text-xs text-gray-600">
                        Certified at <span class="font-medium">{{ $inspection->certified_at?->format('m/d/Y H:i') ?? '—' }}</span>
                        by <span class="font-medium">{{ optional($inspection->certifiedBy)->name ?? '—' }}</span>
                    </div>
                @endif
            </div>

            {{-- TIMES --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-lg border p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Inspection Times</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="rounded border bg-gray-50 p-3">
                            <div class="text-gray-500">Crash Phone Test</div>
                            <div class="font-semibold text-gray-900">{{ $fmtTime($header['crash_phone_test_time'] ?? null) }}</div>
                            <div class="text-xs text-gray-500 mt-1">By: {{ $val($header['by_1'] ?? null) }}</div>
                        </div>

                        <div class="rounded border bg-gray-50 p-3">
                            <div class="text-gray-500">AM Inspection</div>
                            <div class="font-semibold text-gray-900">{{ $fmtTime($header['am_time'] ?? null) }}</div>
                            <div class="text-xs text-gray-500 mt-1">By: {{ $val($header['by_2'] ?? null) }}</div>
                        </div>

                        <div class="rounded border bg-gray-50 p-3">
                            <div class="text-gray-500">PM Inspection</div>
                            <div class="font-semibold text-gray-900">{{ $fmtTime($header['pm_time'] ?? null) }}</div>
                            <div class="text-xs text-gray-500 mt-1">By: {{ $val($header['by_3'] ?? null) }}</div>
                        </div>

                        <div class="rounded border bg-gray-50 p-3">
                            <div class="text-gray-500">Other Inspection</div>
                            <div class="font-semibold text-gray-900">{{ $fmtTime($header['other_time'] ?? null) }}</div>
                            <div class="text-xs text-gray-500 mt-1">By: {{ $val($header['by_4'] ?? null) }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg border p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Checklist Snapshot</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="rounded border p-4">
                            <div class="text-gray-500 font-medium mb-2">AM Counts</div>
                            <div class="flex items-center justify-between"><span>S</span><span class="font-semibold">{{ $amCounts['S'] }}</span></div>
                            <div class="flex items-center justify-between"><span>U</span><span class="font-semibold">{{ $amCounts['U'] }}</span></div>
                            <div class="flex items-center justify-between"><span>N/A</span><span class="font-semibold">{{ $amCounts['NA'] }}</span></div>
                        </div>

                        <div class="rounded border p-4">
                            <div class="text-gray-500 font-medium mb-2">PM Counts</div>
                            <div class="flex items-center justify-between"><span>S</span><span class="font-semibold">{{ $pmCounts['S'] }}</span></div>
                            <div class="flex items-center justify-between"><span>U</span><span class="font-semibold">{{ $pmCounts['U'] }}</span></div>
                            <div class="flex items-center justify-between"><span>N/A</span><span class="font-semibold">{{ $pmCounts['NA'] }}</span></div>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500 mt-3">
                        Counts are based on saved checklist JSON values.
                    </div>
                </div>
            </div>

            {{-- CHECKLIST --}}
            <div class="bg-white shadow-sm rounded-lg border">
                <div class="p-6 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">Checklist</h3>
                    <div class="text-xs text-gray-500">AM/PM show S / U / N/A</div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold border-t border-b">Item</th>
                                <th class="px-4 py-2 text-center font-semibold border-t border-b w-24">AM</th>
                                <th class="px-4 py-2 text-center font-semibold border-t border-b w-24">PM</th>
                                <th class="px-4 py-2 text-left font-semibold border-t border-b">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($checklist as $key => $row)
                                @php
                                    $am = strtoupper((string)($row['am'] ?? ''));
                                    $pm = strtoupper((string)($row['pm'] ?? ''));
                                    $rm = trim((string)($row['remarks'] ?? ''));

                                    $label = $labels[$key] ?? $key;
                                @endphp

                                <tr>
                                    <td class="px-4 py-2 text-gray-900 font-medium">
                                        {{ $label }}
                                        @if(!isset($labels[$key]))
                                            <div class="text-xs text-gray-400 font-normal">{{ $key }}</div>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-flex items-center justify-center w-12 h-7 rounded border text-[11px] font-bold leading-none {{ $badge($am) }}">
                                            {{ $am ?: '—' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-2 text-center">
                                        <span class="inline-flex items-center justify-center w-12 h-7 rounded border text-[11px] font-bold leading-none {{ $badge($pm) }}">
                                            {{ $pm ?: '—' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-2 text-gray-800">{{ $rm }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                        No checklist data recorded.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FINDINGS --}}
            <div class="bg-white shadow-sm rounded-lg border p-6">
                <h3 class="font-semibold text-gray-900 mb-2">General Findings / Notes</h3>
                <div class="whitespace-pre-wrap text-sm text-gray-800">
                    {{ $inspection->findings ?? '—' }}
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
                                    'updated', 'status_changed' => 'bg-blue-50 text-blue-800 ring-blue-600/20',
                                    'certified' => 'bg-indigo-50 text-indigo-800 ring-indigo-600/20',
                                    'unlocked' => 'bg-gray-50 text-gray-800 ring-gray-600/20',
                                    'deleted' => 'bg-red-50 text-red-800 ring-red-600/20',
                                    default => 'bg-gray-50 text-gray-800 ring-gray-600/20',
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
                                    @if(empty($changes))
                                        <div class="text-sm text-gray-600">No meaningful field changes recorded.</div>
                                    @else
                                        <div class="text-xs uppercase font-semibold text-gray-500">Key changes</div>
                                        <ul class="mt-2 space-y-1 text-sm">
                                            @foreach($changes as $k => $chg)
                                                <li>
                                                    <span class="font-semibold">{{ $humanKey($k) }}:</span>
                                                    <span class="text-gray-600">{{ $fmt($chg['old']) }}</span>
                                                    <span class="mx-1 text-gray-400 font-black">→</span>
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
</x-app-layout>
