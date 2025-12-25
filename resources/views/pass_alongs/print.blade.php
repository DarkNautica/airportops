{{-- resources/views/pass_alongs/print.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pass Along #{{ $passAlong->id }}</title>

    <style>
        @page { margin: 18px 20px; }

        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: 700; }
        .sub { font-size: 12px; margin-top: 2px; color: #444; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #222; padding: 6px; vertical-align: top; }

        th { background: #f2f2f2; font-weight: 700; }

        .label { width: 25%; font-weight: 700; }
        .section { margin-top: 14px; }
        .pagebreak { page-break-before: always; }
    </style>
</head>
<body>

<div class="center">
    <div class="title">Airport Operations — Daily Checklist & Pass-Along</div>
    <div class="sub">Pass Along #{{ $passAlong->id }} • {{ optional($passAlong->date)->format('Y-m-d') }}</div>
</div>

<table>
    <tr>
        <td class="label">Specialist</td>
        <td>{{ $passAlong->specialist_name ?? '' }}</td>
        <td class="label">Shift</td>
        <td>{{ $passAlong->shift_start_time ?? '' }} – {{ $passAlong->shift_end_time ?? '' }}</td>
    </tr>
</table>

<div class="section">
    <table>
        <tr>
            <th colspan="2">Daily Task Checklist</th>
        </tr>
        @php
            $items = [
                'am_part_139' => 'AM Part-139 Inspection',
                'am_perimeter' => 'AM Perimeter Inspection',
                'am_terminal' => 'AM Terminal Inspection',
                'pm_part_139' => 'PM Part-139 Inspection',
                'pm_terminal' => 'PM Terminal Inspection',
                'ramp_apron_patrol' => 'Ramp / Apron Patrol',
                'wildlife_patrol' => 'Wildlife Patrol',
            ];
        @endphp
        @foreach($items as $k => $label)
            <tr>
                <td>{{ $label }}</td>
                <td style="text-align:center">{{ $passAlong->$k ? '✓' : '' }}</td>
            </tr>
        @endforeach
    </table>
</div>

<div class="section">
    <table>
        <tr><th>Significant Activity / Watch Items</th></tr>
        <tr><td style="min-height:60px;">{{ $passAlong->significant_activity ?? '' }}</td></tr>
    </table>
</div>

@foreach(($passAlong->sections ?? []) as $section)
    <div class="pagebreak"></div>

    <table>
        <tr><th colspan="2">{{ $section['title'] ?? 'Pass Along' }}</th></tr>
        <tr>
            <th style="width:35%;">Label</th>
            <th style="width:65%;">Value</th>
        </tr>
        @foreach(($section['rows'] ?? []) as $row)
            <tr>
                <td>{{ $row['label'] ?? '' }}</td>
                <td>{{ $row['value'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
@endforeach

</body>
</html>
