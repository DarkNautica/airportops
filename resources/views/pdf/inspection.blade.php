<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $inspection->insp_number }} - Part 139 Daily Inspection</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111; }
        .title { text-align:center; font-weight:700; font-size: 16px; margin-bottom: 6px; }
        .subtitle { text-align:center; font-weight:700; margin-bottom: 10px; }
        .meta { width:100%; margin-bottom: 10px; }
        .meta td { padding: 2px 4px; vertical-align: top; }
        .box { border:1px solid #222; padding:6px; }
        table.check { width:100%; border-collapse: collapse; margin-bottom: 10px; }
        table.check th, table.check td { border: 1px solid #222; padding: 4px; }
        table.check th { background:#f3f3f3; text-align:left; }
        .center { text-align:center; }
        .muted { color:#444; }
        .section { font-weight:700; background:#eaeaea; }
    </style>
</head>
<body>

@php
    $header = $inspection->header ?? [];
    $checklist = $inspection->checklist ?? [];
@endphp

<div class="title">ASHEVILLE REGIONAL AIRPORT</div>
<div class="subtitle">AIRPORT SAFETY SELF-INSPECTION CHECKLIST<br><span class="muted">(FAA Part 139 – Daily Inspection)</span></div>

<table class="meta">
    <tr>
        <td class="box" style="width:50%;">
            <div><strong>Date:</strong> {{ \Illuminate\Support\Carbon::parse($inspection->inspection_date)->format('m / d / Y') }}</div>
            <div><strong>Day:</strong> {{ $header['day'] ?? '' }}</div>
        </td>
        <td class="box" style="width:50%;">
            <div><strong>Crash Phone Test:</strong> {{ $header['crash_phone_time'] ?? '' }}</div>
            <div><strong>AM Inspection:</strong> {{ $header['am_time'] ?? '' }}</div>
            <div><strong>PM Inspection:</strong> {{ $header['pm_time'] ?? '' }}</div>
            <div><strong>Other Inspection:</strong> {{ $header['other_time'] ?? '' }}</div>
        </td>
    </tr>
    <tr>
        <td class="box">
            <div><strong>By (AM):</strong> {{ $header['by_am'] ?? '' }}</div>
            <div><strong>By (PM):</strong> {{ $header['by_pm'] ?? '' }}</div>
            <div><strong>By (Other):</strong> {{ $header['by_other'] ?? '' }}</div>
        </td>
        <td class="box">
            <div><strong>Overall:</strong>
                {{ $header['overall'] ?? '' }}
                <span class="muted">(Satisfactory / Unsatisfactory)</span>
            </div>
        </td>
    </tr>
</table>

<div class="section box">FACILITIES / CONDITIONS</div>

@php
    // Sections + items match your checklist structure
    $sections = [
        'PAVEMENT AREAS' => [
            'pavement_lip_over_3' => 'Pavement lip over 3"',
            'holes_over_5' => 'Holes > 5" dia, > 3" deep',
            'cracks_spalling_bumps' => 'Cracks / spalling / bumps',
            'fod' => 'FOD (gravel, debris, etc.)',
            'rubber_deposits' => 'Rubber deposits',
            'ponding_edge_dams' => 'Ponding / edge dams',
        ],
        'SAFETY AREAS' => [
            'ruts_humps_erosion' => 'Ruts / humps / erosion',
            'drainage_construction' => 'Drainage / construction',
            'objects_frangible_base' => 'Objects / frangible base',
        ],
        'MARKINGS / SIGNS' => [
            'visibility_standard' => 'Visibility standard',
            'hold_lines_signs' => 'Hold lines / signs',
            'frangible_signs' => 'Frangible signs',
        ],
        'LIGHTING' => [
            'obscured_dirty_fading' => 'Obscured / dirty / fading',
            'damaged_missing' => 'Damaged / missing',
            'inoperative' => 'Inoperative',
            'faulty_aim_adjustment' => 'Faulty aim / adjustment',
        ],
        'NAVIGATIONAL AIDS' => [
            'rotating_beacon' => 'Rotating beacon',
            'wind_indicators' => 'Wind indicators',
            'reils_papi_ils' => 'REILs / PAPI / ILS systems',
        ],
        'OBSTRUCTIONS' => [
            'obstruction_lights' => 'Obstruction lights',
            'cranes_trees' => 'Cranes / trees',
        ],
        'WILDLIFE HAZARDS' => [
            'wildlife_present' => 'Wildlife present / location',
            'complying_whmp' => 'Complying with WHMP',
        ],
        'FUEL FARMS' => [
            'fuel_fencing_gates_signs' => 'Fencing / gates / signs',
            'fuel_marking_labeling' => 'Fuel marking / labeling',
            'fuel_fire_ext_ground_clips' => 'Fire exting. / ground clips',
            'fuel_leaks_vegetation' => 'Fuel leaks / vegetation',
        ],
        'SNOW & ICE' => [
            'surface_conditions' => 'Surface conditions',
            'snow_bank_clearance' => 'Snow bank clearance',
            'lights_signs_obscured' => 'Lights / signs obscured',
            'navaids_fire_access' => 'Navaids / fire access',
        ],
        'ARFF' => [
            'equipment_crew_availability' => 'Equipment / crew availability',
            'response_routes_clear' => 'Response routes clear',
        ],
        'PUBLIC PROTECTION' => [
            'public_fencing_gates_signs' => 'Fencing / gates / signs',
            'unauthorized_persons_vehicles' => 'Unauthorized persons / veh.',
        ],
        'CONSTRUCTION' => [
            'barricades_lights' => 'Barricades / lights',
            'equipment_parking' => 'Equipment parking',
        ],
    ];
@endphp

@foreach ($sections as $sectionTitle => $items)
    <table class="check">
        <thead>
        <tr>
            <th colspan="4">{{ $sectionTitle }}</th>
        </tr>
        <tr>
            <th style="width:45%;">Item</th>
            <th class="center" style="width:10%;">AM</th>
            <th class="center" style="width:10%;">PM</th>
            <th style="width:35%;">Remarks</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $key => $label)
            @php
                $row = $checklist[$key] ?? [];
                $am = !empty($row['am']);
                $pm = !empty($row['pm']);
                $remarks = $row['remarks'] ?? '';
            @endphp
            <tr>
                <td>{{ $label }}</td>
                <td class="center">{{ $am ? '☒' : '☐' }}</td>
                <td class="center">{{ $pm ? '☒' : '☐' }}</td>
                <td>{{ $remarks }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endforeach

<div class="muted">
    Generated: {{ now()->format('Y-m-d H:i') }} • {{ $inspection->insp_number }}
</div>

</body>
</html>
