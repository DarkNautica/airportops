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
        .state-box { display:inline-block; width:14px; height:14px; line-height:14px; border:1px solid #111; text-align:center; font-weight:700; font-size:9px; vertical-align:middle; }
        .state-box.na { font-size:7.5px; letter-spacing:-0.3px; }
    </style>
</head>
<body>

@php
    $header = is_array($inspection->header ?? null) ? $inspection->header : [];
    $checklist = is_array($inspection->checklist ?? null) ? $inspection->checklist : [];
    $sections = config('checklist');

    $fmtDate = function($d) {
        try { return $d ? \Illuminate\Support\Carbon::parse($d)->format('m/d/Y') : '—'; }
        catch (\Throwable $e) { return '—'; }
    };
    $fmtTime = function($t) {
        try { return $t ? \Illuminate\Support\Carbon::parse($t)->format('H:i') : '—'; }
        catch (\Throwable $e) { return '—'; }
    };
    $val = fn($v) => filled($v ?? null) ? $v : '—';

    $state = function(string $key, string $period) use ($checklist): ?string {
        $v = data_get($checklist, "{$key}.{$period}", null);
        if (in_array($v, ['S','U','NA'], true)) return $v;
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
@endphp

<div class="title">{{ $header['airport_name'] ?? 'ASHEVILLE REGIONAL AIRPORT' }}</div>
<div class="subtitle">AIRPORT SAFETY SELF-INSPECTION CHECKLIST<br><span class="muted">(FAA Part 139 – Daily Inspection)</span></div>

<table class="meta">
    <tr>
        <td class="box" style="width:50%;">
            <div><strong>Date:</strong> {{ $fmtDate($inspection->inspection_date) }}</div>
            <div><strong>Day:</strong> {{ $val($header['inspection_day'] ?? null) }}</div>
            <div><strong>Inspection #:</strong> {{ $val($inspection->insp_number) }}</div>
            <div><strong>Inspector:</strong> {{ $inspection->inspector?->name ?? '—' }}</div>
        </td>
        <td class="box" style="width:50%;">
            <div><strong>Crash Phone Test:</strong> {{ $fmtTime($header['crash_phone_test_time'] ?? null) }}</div>
            <div><strong>AM Inspection:</strong> {{ $fmtTime($header['am_time'] ?? null) }}</div>
            <div><strong>PM Inspection:</strong> {{ $fmtTime($header['pm_time'] ?? null) }}</div>
            <div><strong>Other Inspection:</strong> {{ $fmtTime($header['other_time'] ?? null) }}</div>
        </td>
    </tr>
    <tr>
        <td class="box">
            <div><strong>By (Crash):</strong> {{ $val($header['by_1'] ?? null) }}</div>
            <div><strong>By (AM):</strong> {{ $val($header['by_2'] ?? null) }}</div>
            <div><strong>By (PM):</strong> {{ $val($header['by_3'] ?? null) }}</div>
            <div><strong>By (Other):</strong> {{ $val($header['by_4'] ?? null) }}</div>
        </td>
        <td class="box">
            <div><strong>Overall:</strong>
                {{ $val($header['overall_status'] ?? null) }}
                <span class="muted">(Satisfactory / Unsatisfactory)</span>
            </div>
        </td>
    </tr>
</table>

<div class="section box">FACILITIES / CONDITIONS</div>

@foreach ($sections as $sectionTitle => $items)
    <table class="check">
        <thead>
        <tr><th colspan="4">{{ $sectionTitle }}</th></tr>
        <tr>
            <th style="width:45%;">Item</th>
            <th class="center" style="width:10%;">AM</th>
            <th class="center" style="width:10%;">PM</th>
            <th style="width:35%;">Remarks</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $item)
            @php
                $key = $item['key'];
                $am = $state($key, 'am');
                $pm = $state($key, 'pm');
                $rm = $remarks($key);
            @endphp
            <tr>
                <td>{{ $item['label'] }}</td>
                <td class="center">
                    <span class="state-box {{ $isNA($am) ? 'na' : '' }}">{{ $printState($am) }}</span>
                </td>
                <td class="center">
                    <span class="state-box {{ $isNA($pm) ? 'na' : '' }}">{{ $printState($pm) }}</span>
                </td>
                <td>{{ $rm }}</td>
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
