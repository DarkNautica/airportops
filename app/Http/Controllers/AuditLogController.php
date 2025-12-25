<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $data = $request->validate([
            'q'          => ['nullable', 'string', 'max:200'],
            'event'      => ['nullable', 'string', 'max:80'],
            'type'       => ['nullable', 'string', 'max:255'], // auditable_type
            'causer_id'  => ['nullable', 'integer'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date'],
            'ip'         => ['nullable', 'string', 'max:45'],
        ]);

        $filters = $this->normalizeFilters($data);

        $query = $this->buildQuery($filters, $request);

        $auditLogs = $query->paginate(25)->withQueryString();

        $eventOptions = AuditLog::query()
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $typeOptions = AuditLog::query()
            ->select('auditable_type')
            ->whereNotNull('auditable_type')
            ->distinct()
            ->orderBy('auditable_type')
            ->pluck('auditable_type');

        return view('audit_logs.index', [
            'auditLogs'    => $auditLogs,
            'eventOptions' => $eventOptions,
            'typeOptions'  => $typeOptions,
            'filters'      => $filters,
        ]);
    }

    /**
     * Optional: HTML print view (target="_blank" usually)
     */
    public function print(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $data = $request->validate([
            'q'          => ['nullable', 'string', 'max:200'],
            'event'      => ['nullable', 'string', 'max:80'],
            'type'       => ['nullable', 'string', 'max:255'],
            'causer_id'  => ['nullable', 'integer'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date'],
            'ip'         => ['nullable', 'string', 'max:45'],
        ]);

        $filters = $this->normalizeFilters($data);
        $canSensitive = $request->user()?->can('viewSensitive', AuditLog::class) ?? false;

        $query = $this->buildQuery($filters, $request)->latest();

        $max = 2000;
        $count = (clone $query)->count();
        abort_if($count > $max, 422, "Too many rows to print ($count). Narrow filters (max $max).");

        $auditLogs = $query->get();

        return view('audit_logs.print', compact('auditLogs', 'filters', 'canSensitive'));
    }

    public function exportPdf(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $data = $request->validate([
            'q'          => ['nullable', 'string', 'max:200'],
            'event'      => ['nullable', 'string', 'max:80'],
            'type'       => ['nullable', 'string', 'max:255'],
            'causer_id'  => ['nullable', 'integer'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date'],
            'ip'         => ['nullable', 'string', 'max:45'],
        ]);

        $filters = $this->normalizeFilters($data);
        $canSensitive = $request->user()?->can('viewSensitive', AuditLog::class) ?? false;

        $query = $this->buildQuery($filters, $request)->latest();

        $max = 2000;
        $count = (clone $query)->count();
        abort_if($count > $max, 422, "Too many rows to export ($count). Narrow filters (max $max).");

        $auditLogs = $query->get();

        $pdf = Pdf::loadView('audit_logs.print', [
            'auditLogs'    => $auditLogs,
            'filters'      => $filters,
            'canSensitive' => $canSensitive,
        ])->setPaper('letter', 'portrait');

        return $pdf->download('audit_logs_' . now()->format('Ymd_His') . '.pdf');
    }

    public function exportCsv(Request $request)
    {
        $this->authorize('viewAny', AuditLog::class);

        $data = $request->validate([
            'q'          => ['nullable', 'string', 'max:200'],
            'event'      => ['nullable', 'string', 'max:80'],
            'type'       => ['nullable', 'string', 'max:255'],
            'causer_id'  => ['nullable', 'integer'],
            'date_from'  => ['nullable', 'date'],
            'date_to'    => ['nullable', 'date'],
            'ip'         => ['nullable', 'string', 'max:45'],
        ]);

        $filters = $this->normalizeFilters($data);
        $canSensitive = $request->user()?->can('viewSensitive', AuditLog::class) ?? false;

        $query = $this->buildQuery($filters, $request)->latest();

        $max = 10000;
        $count = (clone $query)->count();
        abort_if($count > $max, 422, "Too many rows to export ($count). Narrow filters (max $max).");

        $filename = 'audit_logs_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query, $canSensitive) {
            $out = fopen('php://output', 'w');

            $headers = ['event', 'subject_type', 'subject_id', 'user', 'timestamp', 'changes'];
            if ($canSensitive) $headers = array_merge($headers, ['ip', 'user_agent']);
            fputcsv($out, $headers);

            $query->chunk(1000, function ($logs) use ($out, $canSensitive) {
                foreach ($logs as $log) {
                    $props = is_array($log->properties ?? null) ? $log->properties : [];
                    $changes = $this->summarizeChanges($props);

                    $row = [
                        (string) $log->event,
                        class_basename((string) $log->auditable_type),
                        (string) ($log->auditable_id ?? ''),
                        $log->causer?->name ?? ($log->causer_id ? 'User #' . $log->causer_id : 'System'),
                        optional($log->created_at)->format('Y-m-d H:i:s'),
                        $changes,
                    ];

                    if ($canSensitive) {
                        $row[] = (string) ($log->ip ?? '');
                        $row[] = (string) ($log->user_agent ?? '');
                    }

                    fputcsv($out, $row);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ============================================================
    // Helpers
    // ============================================================

    private function normalizeFilters(array $data): array
    {
        $q         = trim((string)($data['q'] ?? ''));
        $event     = trim((string)($data['event'] ?? ''));
        $type      = trim((string)($data['type'] ?? ''));
        $causerId  = $data['causer_id'] ?? null;
        $dateFrom  = $data['date_from'] ?? null;
        $dateTo    = $data['date_to'] ?? null;
        $ip        = trim((string)($data['ip'] ?? ''));

        return [
            'q' => $q,
            'event' => $event,
            'type' => $type,
            'causer_id' => $causerId,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'ip' => $ip,
        ];
    }

    /**
     * This is YOUR query, kept intact:
     * - JSON_SEARCH across properties ($**)
     * - exact auditable_id match if numeric
     * - event LIKE fallback
     *
     * But now it also:
     * - blocks IP filtering unless you have viewSensitive permission
     * - is shared by index/print/pdf/csv so exports match filters
     */
    private function buildQuery(array $filters, Request $request)
    {
        $q         = $filters['q'];
        $event     = $filters['event'];
        $type      = $filters['type'];
        $causerId  = $filters['causer_id'];
        $dateFrom  = $filters['date_from'];
        $dateTo    = $filters['date_to'];
        $ip        = $filters['ip'];

        $canSensitive = $request->user()?->can('viewSensitive', AuditLog::class) ?? false;

        return AuditLog::query()
            ->with(['causer'])
            ->when($event !== '', fn($qq) => $qq->where('event', $event))
            ->when($type !== '', fn($qq) => $qq->where('auditable_type', $type))
            ->when(!empty($causerId), fn($qq) => $qq->where('causer_id', $causerId))
            ->when($ip !== '' && $canSensitive, fn($qq) => $qq->where('ip', $ip))
            ->when($dateFrom, fn($qq) => $qq->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn($qq) => $qq->whereDate('created_at', '<=', $dateTo))
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($sub) use ($q) {
                    if (ctype_digit($q)) {
                        $sub->orWhere('auditable_id', (int)$q);
                    }

                    // MySQL JSON search anywhere inside properties JSON
                    $sub->orWhereRaw(
                        "JSON_SEARCH(properties, 'one', ?, NULL, '$**') IS NOT NULL",
                        [$q]
                    );

                    $sub->orWhere('event', 'like', "%{$q}%");
                });
            })
            ->latest();
    }

    private function summarizeChanges(array $props): string
    {
        $old = is_array($props['old'] ?? null) ? $props['old'] : [];
        $new = is_array($props['new'] ?? null) ? $props['new'] : [];

        $keys = $props['dirty'] ?? array_unique(array_merge(array_keys($old), array_keys($new)));
        $keys = is_array($keys) ? $keys : [];

        // Don’t spam exports with massive blobs
        $skipTopLevel = ['header', 'checklist', 'properties'];
        $ignore = ['created_at', 'updated_at'];

        $pairs = [];

        foreach ($keys as $k) {
            if (!is_string($k)) continue;
            if (in_array($k, $ignore, true)) continue;

            // Handle large JSON fields intelligently
            if (in_array($k, $skipTopLevel, true)) {
                // If header/checklist exist, emit a compact summary instead of raw JSON
                $summary = $this->summarizeJsonField($k, $old[$k] ?? null, $new[$k] ?? null);
                if ($summary !== null) {
                    $pairs[] = $summary;
                }
                continue;
            }

            $o = $this->scalar($old[$k] ?? null);
            $n = $this->scalar($new[$k] ?? null);

            if ($o === $n) continue;

            $pairs[] = "{$k}: {$o} → {$n}";
            if (count($pairs) >= 10) break; // slightly higher than before, still readable
        }

        return $pairs ? implode(' | ', $pairs) : '—';
    }

    /**
     * Summarize large JSON fields (header/checklist) without dumping the whole object.
     * Produces compact lines like:
     *  - header changed: crash_phone_test_time, am_time, by_2 (+2 more)
     *  - checklist changed: 4 items (fod.pm, rubber_deposits.pm, lighting_obscured_dirty_fading.remarks, ...)
     */
    private function summarizeJsonField(string $field, $oldVal, $newVal): ?string
    {
        $oldArr = is_array($oldVal) ? $oldVal : (is_string($oldVal) ? json_decode($oldVal, true) : null);
        $newArr = is_array($newVal) ? $newVal : (is_string($newVal) ? json_decode($newVal, true) : null);

        if (!is_array($oldArr)) $oldArr = [];
        if (!is_array($newArr)) $newArr = [];

        // For header: top-level scalar keys are enough
        if ($field === 'header') {
            $changed = [];
            $allKeys = array_unique(array_merge(array_keys($oldArr), array_keys($newArr)));

            foreach ($allKeys as $k) {
                if (!is_string($k)) continue;
                $o = $this->scalar($oldArr[$k] ?? null);
                $n = $this->scalar($newArr[$k] ?? null);
                if ($o === $n) continue;
                $changed[] = $k;
                if (count($changed) >= 6) break;
            }

            if (!$changed) return null;

            $more = count(array_unique(array_merge(array_keys($oldArr), array_keys($newArr)))) - count($changed);
            $tail = $more > 0 ? " (+more)" : "";

            return "header changed: " . implode(', ', $changed) . $tail;
        }

        // For checklist: flatten to meaningful leaf changes (am/pm/remarks)
        if ($field === 'checklist') {
            $changedPaths = [];

            $items = array_unique(array_merge(array_keys($oldArr), array_keys($newArr)));
            foreach ($items as $itemKey) {
                if (!is_string($itemKey)) continue;

                $oItem = is_array($oldArr[$itemKey] ?? null) ? $oldArr[$itemKey] : [];
                $nItem = is_array($newArr[$itemKey] ?? null) ? $newArr[$itemKey] : [];

                foreach (['am','pm','remarks'] as $subKey) {
                    $o = $this->scalar($oItem[$subKey] ?? null);
                    $n = $this->scalar($nItem[$subKey] ?? null);
                    if ($o === $n) continue;

                    $changedPaths[] = "{$itemKey}.{$subKey}";
                    if (count($changedPaths) >= 6) break 2;
                }
            }

            if (!$changedPaths) return null;

            return "checklist changed: " . count($changedPaths) . " fields (" . implode(', ', $changedPaths) . (count($changedPaths) >= 6 ? ', …' : '') . ")";
        }

        return null;
    }


    private function scalar($v): string
    {
        if (is_null($v) || $v === '') return '—';
        if (is_bool($v)) return $v ? 'true' : 'false';
        if (is_array($v)) return '[json]';

        $s = trim((string)$v);

        // normalize ISO timestamps
        if (preg_match('/^\d{4}-\d{2}-\d{2}T/', $s)) return substr($s, 0, 19) . 'Z';

        return $s === '' ? '—' : $s;
    }
}
