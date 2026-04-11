{{-- resources/views/inspections/print.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Inspection {{ $inspection->insp_number }}</title>

    <style>
        @page { margin: 18px 20px; }

        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: 700; letter-spacing: 0.3px; }
        .subtitle { font-size: 13px; font-weight: 700; margin-top: 2px; }
        .subnote { font-size: 10px; color: #444; margin-top: 2px; }

        /* META */
        table.meta { margin-top: 12px; width: 100%; border-collapse: collapse; }
        .meta td { padding: 4px 6px; vertical-align: middle; }
        .meta .label { width: 10%; font-weight: 700; color: #222; }
        .meta .value { width: 23%; border-bottom: 1px solid #bbb; }

        /* forces values to sit “on the line” consistently (fixes the high text look) */
        .linebox { height: 16px; line-height: 16px; display: block; padding-top: 1px; }

        /* TIME BLOCKS */
        table.times {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            border-top: 1px solid #bbb;
            border-bottom: 1px solid #bbb;
        }
        table.times th, table.times td { padding: 4px 6px; }
        table.times th {
            font-size: 10px;
            font-weight: 700;
            text-align: center; /* ✅ centered headings */
            color: #222;
        }
        table.times td { vertical-align: middle; }

        .sep-right { border-right: 1px solid #bbb; } /* ✅ visual separators */

        .time-label { width: 6%; font-weight: 700; }
        .time-val   { width: 10%; border-bottom: 1px solid #bbb; }
        .by-label   { width: 4%; font-weight: 700; }
        .by-val     { width: 15%; border-bottom: 1px solid #bbb; }

        /* CHECKLIST */
        .section-header { margin-top: 12px; font-weight: 700; color: #111; }

        table.checklist { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.checklist th, table.checklist td { border: 1px solid #222; padding: 5px 6px; vertical-align: middle; }
        table.checklist th { background: #f2f2f2; font-weight: 700; }

        .col-item { width: 68%; }
        .col-state { width: 6%; text-align: center; }
        .col-remarks { width: 20%; }

        /* AM/PM state box (S/U/N/A) — tight + centered for DOMPDF */
        .state-box{
            display:inline-block;
            width: 14px;
            height: 14px;
            line-height: 14px;
            border:1px solid #111;
            text-align:center;
            font-weight:700;
            font-size: 9px;
            vertical-align: middle;
        }
        .state-box.na {
            font-size: 7.5px; /* N/A fits */
            letter-spacing: -0.3px;
        }

        /* NOTES */
        .notes {
            margin-top: 10px;
            border: 1px solid #222;
            padding: 8px;
            min-height: 60px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

@php
    $sections = config('checklist');

    $header = is_array($inspection->header ?? null) ? $inspection->header : [];
    $checklist = is_array($inspection->checklist ?? null) ? $inspection->checklist : [];

    $fmtDate = function($d) {
        try { return $d ? \Illuminate\Support\Carbon::parse($d)->format('m/d/Y') : '—'; }
        catch (\Throwable $e) { return '—'; }
    };

    $fmtTime = function($t) {
        try { return $t ? \Illuminate\Support\Carbon::parse($t)->format('H:i') : '—'; }
        catch (\Throwable $e) { return '—'; }
    };

    $val = fn($v) => filled($v ?? null) ? $v : '—';

    // tri-state fetch (S/U/NA) + legacy checkbox fallback (1 => U)
    $state = function(string $key, string $period) use ($checklist): ?string {
        $v = data_get($checklist, "{$key}.{$period}", null);

        if (in_array($v, ['S','U','NA'], true)) return $v;

        // legacy: checkbox checked means an issue was observed
        if ($v === 1 || $v === true || $v === '1') return 'U';

        return null;
    };

    $printState = function(?string $s): string {
        if ($s === 'NA') return 'N/A';
        return $s ?? '';
    };

    $isNA = fn(?string $s) => $s === 'NA';

    $remarks = function(string $key) use ($checklist): string {
        $r = data_get($checklist, "{$key}.remarks", '');
        return is_string($r) ? trim($r) : '';
    };

    // Header fields
    $airportName = $header['airport_name'] ?? 'ASHEVILLE REGIONAL AIRPORT';
    $day = $header['inspection_day'] ?? null;
    $overall = $header['overall_status'] ?? null;

    $crashTime = $header['crash_phone_test_time'] ?? null;
    $amTime = $header['am_time'] ?? null;
    $pmTime = $header['pm_time'] ?? null;
    $otherTime = $header['other_time'] ?? null;

    // Uses your existing create inputs
    $byCrash = $header['by_1'] ?? null;
    $byAM    = $header['by_2'] ?? null;
    $byPM    = $header['by_3'] ?? null;
    $byOther = $header['by_4'] ?? null;
@endphp

{{-- HEADER --}}
<div class="center">
    <div class="title">{{ $airportName }}</div>
    <div class="subtitle">AIRPORT SAFETY SELF-INSPECTION CHECKLIST</div>
    <div class="subnote">(FAA Part 139 – Daily Inspection)</div>
</div>

{{-- META --}}
<table class="meta">
    <tr>
        <td class="label">Date:</td>
        <td class="value"><span class="linebox">{{ $fmtDate($inspection->inspection_date ?? null) }}</span></td>

        <td class="label">Day:</td>
        <td class="value"><span class="linebox">{{ $val($day) }}</span></td>

        <td class="label">Overall:</td>
        <td class="value"><span class="linebox">{{ $val($overall) }}</span></td>
    </tr>
    <tr>
        <td class="label">Inspection #:</td>
        <td class="value"><span class="linebox">{{ $val($inspection->insp_number ?? null) }}</span></td>

        <td class="label">Inspector:</td>
        <td class="value"><span class="linebox">{{ $inspection->inspector?->name ?? '—' }}</span></td>

        <td class="label">&nbsp;</td>
        <td class="value">&nbsp;</td>
    </tr>
</table>

{{-- TIME BLOCKS (centered headings + separators) --}}
<table class="times">
    <tr>
        <th colspan="4" class="sep-right">Crash Phone Test</th>
        <th colspan="4" class="sep-right">AM Inspection</th>
        <th colspan="4" class="sep-right">PM Inspection</th>
        <th colspan="4">Other Inspection</th>
    </tr>
    <tr>
        {{-- Crash --}}
        <td class="time-label">Time:</td>
        <td class="time-val">{{ $fmtTime($crashTime) }}</td>
        <td class="by-label">By:</td>
        <td class="by-val sep-right">{{ $val($byCrash) }}</td>

        {{-- AM --}}
        <td class="time-label">Time:</td>
        <td class="time-val">{{ $fmtTime($amTime) }}</td>
        <td class="by-label">By:</td>
        <td class="by-val sep-right">{{ $val($byAM) }}</td>

        {{-- PM --}}
        <td class="time-label">Time:</td>
        <td class="time-val">{{ $fmtTime($pmTime) }}</td>
        <td class="by-label">By:</td>
        <td class="by-val sep-right">{{ $val($byPM) }}</td>

        {{-- Other --}}
        <td class="time-label">Time:</td>
        <td class="time-val">{{ $fmtTime($otherTime) }}</td>
        <td class="by-label">By:</td>
        <td class="by-val">{{ $val($byOther) }}</td>
    </tr>
</table>

<div class="section-header">FACILITIES / CONDITIONS</div>

{{-- CHECKLIST --}}
@foreach ($sections as $sectionTitle => $items)
    <div style="margin-top: 8px;">
        <table class="checklist">
            <tr>
                <th class="col-item">{{ $sectionTitle }}</th>
                <th class="col-state" style="text-align:center;">AM</th>
                <th class="col-state" style="text-align:center;">PM</th>
                <th class="col-remarks" style="text-align:center;">Remarks</th>
            </tr>

            @foreach ($items as $item)
                @php
                    $key = $item['key'];
                    $am = $state($key, 'am');   // S/U/NA
                    $pm = $state($key, 'pm');   // S/U/NA
                    $rm = $remarks($key);
                @endphp
                <tr>
                    <td class="col-item">{{ $item['label'] }}</td>

                    <td class="col-state">
                        <span class="state-box {{ $isNA($am) ? 'na' : '' }}">{{ $printState($am) }}</span>
                    </td>

                    <td class="col-state">
                        <span class="state-box {{ $isNA($pm) ? 'na' : '' }}">{{ $printState($pm) }}</span>
                    </td>

                    <td class="col-remarks">{{ $rm }}</td>
                </tr>
            @endforeach
        </table>
    </div>
@endforeach

{{-- FINDINGS --}}
<div class="section-header">General Findings / Notes</div>
<div class="notes">{{ $inspection->findings ?? '' }}</div>

</body>
</html>
