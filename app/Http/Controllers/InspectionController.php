<?php

namespace App\Http\Controllers;

use App\Models\Inspection;
use App\Observers\InspectionObserver;
use Illuminate\Http\Request;
use App\Http\Requests\StoreInspectionRequest;
use App\Http\Requests\UpdateInspectionRequest;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class InspectionController extends Controller
{
    public function __construct()
    {
        // Automatically applies policy checks for:
        // index(viewAny), show(view), create(create), store(create),
        // edit(update), update(update), destroy(delete)
        $this->authorizeResource(Inspection::class, 'inspection');
    }

    public function index(Request $request)
    {
        // authorizeResource handles viewAny, but this is explicit clarity if you want:
        // $this->authorize('viewAny', Inspection::class);

        $query = Inspection::query()->with('inspector');

        if ($request->filled('date_from')) {
            $query->whereDate('inspection_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('inspection_date', '<=', $request->date_to);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('insp_number', 'like', "%{$q}%")
                    ->orWhere('inspection_type', 'like', "%{$q}%");
            });
        }

        $inspections = $query
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('inspections.index', compact('inspections'));
    }

    public function create()
    {
        // authorizeResource handles create
        return view('inspections.create');
    }

    public function store(StoreInspectionRequest $request)
    {
        // authorizeResource handles create

        $validated = $request->validated();

        $data = [
            'inspection_date' => $validated['inspection_date'],
            'inspection_type' => 'FAA Part 139 - Daily Inspection',
            'insp_number'     => $this->nextInspectionNumber(),
            'inspector_id'    => Auth::id(),
            'status'          => 'Open',

            'header'    => $this->buildHeaderPayload($validated),
            'checklist' => $this->normalizeChecklist($validated['checklist'] ?? []),

            'findings'  => $validated['findings'] ?? null,
        ];

        $inspection = Inspection::create($data);

        InspectionObserver::logEvent('created', $inspection, [
            'created_by_id' => Auth::id(),
        ]);

        return redirect()
            ->route('inspections.show', $inspection)
            ->with('success', 'Inspection logged.');
    }

    public function show(Inspection $inspection)
    {
        // authorizeResource handles view
        $inspection->load('inspector');

        $auditLogs = AuditLog::query()
            ->with('causer')
            ->where('auditable_type', $inspection->getMorphClass())
            ->where('auditable_id', $inspection->id)
            ->latest()
            ->get();

        return view('inspections.show', compact('inspection', 'auditLogs'));
    }

    public function edit(Inspection $inspection)
    {
        // authorizeResource handles update

        // Optional friendly UX (policy already blocks, but this is clearer)
        if ((bool) $inspection->is_locked) {
            return redirect()
                ->route('inspections.show', $inspection)
                ->with('error', 'Inspection is locked and cannot be edited.');
        }

        return view('inspections.edit', compact('inspection'));
    }

    public function update(UpdateInspectionRequest $request, Inspection $inspection)
    {
        // authorizeResource handles update

        // Defense-in-depth (policy already blocks)
        if ((bool) $inspection->is_locked) {
            return redirect()
                ->route('inspections.show', $inspection)
                ->with('error', 'Inspection is locked and cannot be edited.');
        }

        $validated = $request->validated();

        $before = [
            'inspection_date' => optional($inspection->inspection_date)->format('Y-m-d'),
            'findings' => $inspection->findings,
            'header' => $inspection->header,
            'checklist' => $inspection->checklist,
        ];

        $inspection->update([
            'inspection_date' => $validated['inspection_date'],
            'findings'        => $validated['findings'] ?? null,
            'header'          => $this->buildHeaderPayload($validated),
            'checklist'       => $this->normalizeChecklist($validated['checklist'] ?? []),
        ]);

        InspectionObserver::logEvent('updated', $inspection, [
            'updated_by_id' => Auth::id(),
            'before' => $before,
            'after' => [
                'inspection_date' => optional($inspection->inspection_date)->format('Y-m-d'),
                'findings' => $inspection->findings,
                'header' => $inspection->header,
                'checklist' => $inspection->checklist,
            ],
        ]);

        return redirect()
            ->route('inspections.show', $inspection)
            ->with('success', 'Inspection updated.');
    }

    public function destroy(Inspection $inspection)
    {
        // authorizeResource handles delete

        if ((bool) $inspection->is_locked) {
            return redirect()
                ->route('inspections.show', $inspection)
                ->with('error', 'Inspection is locked and cannot be deleted.');
        }

        InspectionObserver::logEvent('deleted', $inspection, [
            'deleted_by_id' => Auth::id(),
        ]);

        $inspection->delete();

        return redirect()
            ->route('inspections.index')
            ->with('success', 'Inspection deleted.');
    }

    public function certify(Inspection $inspection)
    {
        $this->authorize('certify', $inspection);

        if ((bool) $inspection->is_locked) {
            return redirect()
                ->route('inspections.show', $inspection)
                ->with('success', 'Inspection is already locked.');
        }

        $userId = Auth::id();

        $inspection->update([
            'certified_at' => now(),
            'certified_by' => $userId,

            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => $userId,

            'status' => 'Certified',
        ]);

        InspectionObserver::logEvent('certified', $inspection, [
            'certified_by_id' => $userId,
        ]);

        return redirect()
            ->route('inspections.show', $inspection)
            ->with('success', 'Inspection certified and locked.');
    }

    public function unlock(Inspection $inspection)
    {
        $this->authorize('unlock', $inspection);

        if (!(bool) $inspection->is_locked) {
            return redirect()
                ->route('inspections.show', $inspection)
                ->with('success', 'Inspection is already unlocked.');
        }

        $userId = Auth::id();

        $inspection->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,

            // keep certified_* evidence intact
            'status' => 'Open',
        ]);

        InspectionObserver::logEvent('unlocked', $inspection, [
            'unlocked_by_id' => $userId,
        ]);

        return redirect()
            ->route('inspections.show', $inspection)
            ->with('success', 'Inspection unlocked (admin override).');
    }

    public function print(Inspection $inspection)
    {
        // Protect exports
        $this->authorize('export', $inspection);

        $inspection->load('inspector');

        return view('inspections.print', compact('inspection'));
    }

    public function pdf(Inspection $inspection)
    {
        // Protect exports
        $this->authorize('export', $inspection);

        $inspection->load('inspector');

        $pdf = Pdf::loadView('inspections.print', compact('inspection'))
            ->setPaper('letter', 'portrait');

        $filename = ($inspection->insp_number ?? 'inspection') . '.pdf';

        return $pdf->download($filename);
    }

    // -----------------------------
    // Internal helpers
    // -----------------------------

    private function buildHeaderPayload(array $validated): array
    {
        return [
            'airport_name' => 'ASHEVILLE REGIONAL AIRPORT',

            'inspection_day' => $validated['inspection_day'] ?? null,
            'overall_status' => $validated['overall_status'] ?? null,

            'crash_phone_test_time' => $validated['crash_phone_test_time'] ?? null,
            'am_time' => $validated['am_time'] ?? null,
            'pm_time' => $validated['pm_time'] ?? null,
            'other_time' => $validated['other_time'] ?? null,

            'by_1' => $validated['by_1'] ?? null,
            'by_2' => $validated['by_2'] ?? null,
            'by_3' => $validated['by_3'] ?? null,
            'by_4' => $validated['by_4'] ?? null,
        ];
    }

    private function normalizeChecklist(array $checklist): array
    {
        $out = [];

        foreach ($checklist as $key => $row) {
            if (!is_array($row)) continue;

            $am = strtoupper(trim((string)($row['am'] ?? '')));
            $pm = strtoupper(trim((string)($row['pm'] ?? '')));
            $rm = (string)($row['remarks'] ?? '');

            $out[$key] = [
                'am' => in_array($am, ['S', 'U', 'NA'], true) ? $am : null,
                'pm' => in_array($pm, ['S', 'U', 'NA'], true) ? $pm : null,
                'remarks' => trim($rm) !== '' ? trim($rm) : null,
            ];
        }

        return $out;
    }

    private function nextInspectionNumber(): string
    {
        return DB::transaction(function () {
            $row = DB::table('counters')
                ->where('key', 'inspections.insp_number')
                ->lockForUpdate()
                ->first();

            if (!$row) {
                DB::table('counters')->insert([
                    'key' => 'inspections.insp_number',
                    'value' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $row = (object)['value' => 0];
            }

            $next = ((int)$row->value) + 1;

            DB::table('counters')
                ->where('key', 'inspections.insp_number')
                ->update([
                    'value' => $next,
                    'updated_at' => now(),
                ]);

            return 'INSP-' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
        }, 3); // retries
    }
}
