{{-- resources/views/pass_alongs/print.blade.php --}}
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pass Along #{{ $passAlong->id }}</title>

    <style>
        @page { margin: 18px 20px; }

        body {
            font-family: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
            font-size: 11px;
            color: #0E1520;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #0E1520;
            margin-bottom: 14px;
        }
        .header .brand {
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #64748B;
        }
        .header .title {
            font-family: 'Instrument Serif', Georgia, serif;
            font-size: 18px;
            font-weight: 400;
            color: #0E1520;
            margin-top: 2px;
        }
        .header .sub {
            font-family: monospace;
            font-size: 10px;
            margin-top: 2px;
            color: #64748B;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #E4E9F0;
            padding: 6px 8px;
            vertical-align: top;
            font-size: 11px;
        }
        th {
            background: #F8FAFC;
            font-family: monospace;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #64748B;
            text-align: left;
        }

        .label-cell {
            width: 25%;
            font-weight: 600;
            color: #0E1520;
        }

        .section {
            margin-top: 14px;
        }
        .section-title {
            font-family: monospace;
            font-size: 9px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #64748B;
            margin-bottom: 4px;
        }

        .check-yes {
            color: #059669;
            font-weight: 700;
        }
        .check-no {
            color: #CBD5E1;
        }

        .pagebreak {
            page-break-before: always;
        }

        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="brand">Clear139 &middot; Airport Operations</div>
    <div class="title">Daily Checklist & Pass-Along</div>
    <div class="sub">Pass Along #{{ $passAlong->id }} &middot; {{ optional($passAlong->date)->format('Y-m-d') }}</div>
</div>

<table>
    <tr>
        <td class="label-cell">Specialist</td>
        <td>{{ $passAlong->specialist_name ?? '' }}</td>
        <td class="label-cell">Shift</td>
        <td>{{ $passAlong->shift_start_time ?? '' }} &ndash; {{ $passAlong->shift_end_time ?? '' }}</td>
    </tr>
</table>

<div class="section">
    <div class="section-title">Daily Task Checklist</div>
    <table>
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
                <td style="text-align:center; width:60px;">
                    @if($passAlong->$k)
                        <span class="check-yes">&#10003;</span>
                    @else
                        <span class="check-no">&mdash;</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>

<div class="section">
    <div class="section-title">Significant Activity / Watch Items</div>
    <table>
        <tr><td style="min-height:60px; white-space:pre-wrap;">{{ $passAlong->significant_activity ?? '' }}</td></tr>
    </table>
</div>

@foreach(($passAlong->sections ?? []) as $section)
    <div class="pagebreak"></div>

    <div class="section-title">{{ $section['title'] ?? 'Pass Along' }}</div>
    <table>
        <tr>
            <th style="width:35%;">Item</th>
            <th style="width:65%;">Description</th>
        </tr>
        @foreach(($section['rows'] ?? []) as $row)
            <tr>
                <td class="label-cell">{{ $row['label'] ?? '' }}</td>
                <td style="white-space:pre-wrap;">{{ $row['value'] ?? '' }}</td>
            </tr>
        @endforeach
    </table>
@endforeach

</body>
</html>
