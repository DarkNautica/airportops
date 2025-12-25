<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Audit Logs</title>

    <style>
        @page { margin: 18px 20px; }

        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; color: #111; }
        .center { text-align: center; }
        .title { font-size: 16px; font-weight: 700; letter-spacing: 0.3px; }
        .subnote { font-size: 10px; color: #444; margin-top: 2px; }

        table.meta { margin-top: 10px; width: 100%; border-collapse: collapse; }
        .meta td { padding: 3px 6px; vertical-align: middle; }
        .meta .label { width: 14%; font-weight: 700; color: #222; }
        .meta .value { border-bottom: 1px solid #bbb; }

        table.logs { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.logs th, table.logs td { border: 1px solid #222; padding: 5px 6px; vertical-align: top; }
        table.logs th { background: #f2f2f2; font-weight: 700; font-size: 10px; }

        .small { font-size: 10px; color: #444; }
        .mono { font-family: DejaVu Sans Mono, monospace; font-size: 10px; }
    </style>
</head>
<body>

@php
    // ===== Filters rendering (cleaner) =====
    $filtersStr = collect($filters ?? [])
        ->filter(fn($v) => !is_null($v) && (string)$v !== '')
        ->map(fn($v,$k) => "{$k}={$v}")
        ->implode(', ');
    $filtersStr = $filtersStr !== '' ? $filtersStr : 'None';

    // ===== Scalar formatter (safe for print) =====
    $safe = function($v) {
        if (is_null($v) || $v === '') return '—';
        if (is_bool($v)) return $v ? 'true' : 'false';
        if (is_array($v)) return '[json]';

        $s = trim((string)$v);

        // normalize ISO timestamps (keep compact)
        if (preg_match('/^\d{4}-\d{2}-\d{2}T/', $s)) return substr($s, 0, 10);

        return $s === '' ? '—' : $s;
    };

    // ===== Decode helper for arrays OR JSON strings =====
    $toArray = function ($v): array {
        if (is_array($v)) return $v;
        if (is_string($v)) {
            $d = json_decode($v, true);
            return is_array($d) ? $d : [];
        }
        return [];
    };

    // ===== Compact summary: header =====
    $summarizeHeader = function($oldVal, $newVal) use ($safe, $toArray) {
        $oldArr = $toArray($oldVal);
        $newArr = $toArray($newVal);

        $changed = [];
        $allKeys = array_unique(array_merge(array_keys($oldArr), array_keys($newArr)));

        foreach ($allKeys as $k) {
            if (!is_string($k)) continue;

            $o = $safe($oldArr[$k] ?? null);
            $n = $safe($newArr[$k] ?? null);
            if ($o === $n) continue;

            $changed[] = $k;
            if (count($changed) >= 6) break;
        }

        return $changed
            ? ('header changed: ' . implode(', ', $changed) . (count($allKeys) > 6 ? ', …' : ''))
            : null;
    };

    // ===== Compact summary: checklist (flatten meaningful leaf fields) =====
    $summarizeChecklist = function($oldVal, $newVal) use ($safe, $toArray) {
        $oldArr = $toArray($oldVal);
        $newArr = $toArray($newVal);

        $changed = [];
        $items = array_unique(array_merge(array_keys($oldArr), array_keys($newArr)));

        foreach ($items as $itemKey) {
            if (!is_string($itemKey)) continue;

            $oItem = is_array($oldArr[$itemKey] ?? null) ? $oldArr[$itemKey] : [];
            $nItem = is_array($newArr[$itemKey] ?? null) ? $newArr[$itemKey] : [];

            foreach (['am','pm','remarks'] as $subKey) {
                $o = $safe($oItem[$subKey] ?? null);
                $n = $safe($nItem[$subKey] ?? null);
                if ($o === $n) continue;

                $changed[] = "{$itemKey}.{$subKey}";
                if (count($changed) >= 6) break 2;
            }
        }

        return $changed
            ? ('checklist changed: ' . implode(', ', $changed) . (count($changed) >= 6 ? ', …' : ''))
            : null;
    };

    // ===== Main summarizer (matches UI/CSV philosophy) =====
    $summarize = function($props) use ($safe, $summarizeHeader, $summarizeChecklist) {
        $props = is_array($props) ? $props : [];

        $old = is_array($props['old'] ?? null) ? $props['old'] : [];
        $new = is_array($props['new'] ?? null) ? $props['new'] : [];

        $keys = $props['dirty'] ?? array_unique(array_merge(array_keys($old), array_keys($new)));
        $keys = is_array($keys) ? $keys : [];

        $pairs = [];

        foreach ($keys as $k) {
            if (!is_string($k)) continue;
            if (in_array($k, ['created_at','updated_at'], true)) continue;

            // Never dump these massive fields in print/PDF
            if ($k === 'header') {
                $s = $summarizeHeader($old[$k] ?? null, $new[$k] ?? null);
                if ($s) $pairs[] = $s;
                continue;
            }

            if ($k === 'checklist') {
                $s = $summarizeChecklist($old[$k] ?? null, $new[$k] ?? null);
                if ($s) $pairs[] = $s;
                continue;
            }

            if ($k === 'properties') {
                // ignore, internal
                continue;
            }

            $o = $safe($old[$k] ?? null);
            $n = $safe($new[$k] ?? null);

            if ($o === $n) continue;

            $pairs[] = "{$k}: {$o} → {$n}";
            if (count($pairs) >= 10) break;
        }

        return $pairs ? implode(" | ", $pairs) : '—';
    };

    $labelType = function($type) {
        return match ($type) {
            \App\Models\WorkOrder::class => 'Work Order',
            \App\Models\Inspection::class => 'Inspection',
            default => class_basename((string)$type),
        };
    };
@endphp

<div class="center">
    <div class="title">AUDIT LOGS EXPORT</div>
    <div class="subnote">Generated: {{ now()->format('Y-m-d H:i:s') }}</div>
</div>

<table class="meta">
    <tr>
        <td class="label">Filters:</td>
        <td class="value">{{ $filtersStr }}</td>
    </tr>
</table>

<table class="logs">
    <thead>
    <tr>
        <th style="width: 10%;">Event</th>
        <th style="width: 18%;">Subject</th>
        <th style="width: 14%;">User</th>
        <th style="width: 14%;">When</th>
        @if($canSensitive)
            <th style="width: 12%;">IP</th>
        @endif
        <th>Changes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($auditLogs as $log)
        <tr>
            <td class="mono">{{ strtoupper(str_replace('_',' ', (string)$log->event)) }}</td>

            <td>
                <div><b>{{ $labelType($log->auditable_type) }}</b></div>
                <div class="small">{{ class_basename((string)$log->auditable_type) }} #{{ $log->auditable_id ?? '—' }}</div>
            </td>

            <td>
                <div><b>{{ $log->causer?->name ?? ($log->causer_id ? 'User #'.$log->causer_id : 'System') }}</b></div>
                <div class="small">ID: {{ $log->causer_id ?? '—' }}</div>
            </td>

            <td class="mono">{{ $log->created_at?->format('Y-m-d H:i:s') ?? '—' }}</td>

            @if($canSensitive)
                <td class="mono">{{ $log->ip ?? '—' }}</td>
            @endif

            <td class="mono">{{ $summarize($log->properties) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

</body>
</html>
