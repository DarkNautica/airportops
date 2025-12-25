<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\AuditLog;

class WorkOrderController extends Controller
{
    /**
     * Display a listing of the work orders.
     */
    public function index(Request $request)
    {
        // --------------------
        // Sorting (safe)
        // --------------------
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['id', 'wo_number', 'priority', 'status', 'due_date', 'created_at'];
        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        // --------------------
        // Filters
        // --------------------
        $q = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', 'all');
        $priority = (string) $request->get('priority', 'all');
        $overdue = $request->boolean('overdue');

        $today = now()->toDateString();

        $query = WorkOrder::query();

        // Search filter
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('wo_number', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }

        // Status filter
        if ($status !== '' && $status !== 'all') {
            $query->where('status', $status);
        }

        // Priority filter
        if ($priority !== '' && $priority !== 'all') {
            $query->where('priority', $priority);
        }

        // Overdue filter (exclude Completed/Closed)
        if ($overdue) {
            $query->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->whereNotIn('status', ['Completed', 'Closed']);
        }

        // Apply sorting
        $query->orderBy($sort, $direction);

        // Paginate
        $workOrders = $query
            ->paginate(20)
            ->withQueryString();

        // --------------------
        // Summary counts (KPI cards)
        // --------------------
        // Option A (recommended for demo): KPIs reflect current filters/search
        $baseForSummary = WorkOrder::query();

        // Apply same search + priority filters to KPI base (status-specific KPIs handled below)
        if ($q !== '') {
            $baseForSummary->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('wo_number', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('location', 'like', "%{$q}%");
            });
        }
        if ($priority !== '' && $priority !== 'all') {
            $baseForSummary->where('priority', $priority);
        }
        // NOTE: We intentionally do NOT apply $status or $overdue globally here,
        // because each KPI represents a different "bucket".

        $summary = [
            'open' => (clone $baseForSummary)
                ->where('status', 'Open')
                ->count(),

            'critical' => (clone $baseForSummary)
                ->where('priority', 'Critical')
                ->whereNotIn('status', ['Completed', 'Closed'])
                ->count(),

            'overdue' => (clone $baseForSummary)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', $today)
                ->whereNotIn('status', ['Completed', 'Closed'])
                ->count(),

            'completed' => (clone $baseForSummary)
                ->whereIn('status', ['Completed', 'Closed'])
                ->count(),
        ];

        // If you prefer KPIs ALWAYS global (ignoring filters), replace the block above with:
        // $summary = [
        //     'open' => WorkOrder::where('status', 'Open')->count(),
        //     'critical' => WorkOrder::where('priority', 'Critical')->whereNotIn('status', ['Completed', 'Closed'])->count(),
        //     'overdue' => WorkOrder::whereNotNull('due_date')->whereDate('due_date', '<', $today)->whereNotIn('status', ['Completed', 'Closed'])->count(),
        //     'completed' => WorkOrder::whereIn('status', ['Completed', 'Closed'])->count(),
        // ];

        return view('work_orders.index', [
            'workOrders' => $workOrders,
            'sort' => $sort,
            'direction' => $direction,

            // filters (so blade keeps selected values)
            'q' => $q,
            'status' => $status,
            'priority' => $priority,
            'overdue' => $overdue,

            'summary' => $summary,
        ]);
    }

    public function create()
    {
        return view('work_orders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'priority'    => 'required|string|max:50',
            'status'      => 'required|string|max:50',
            'due_date'    => 'nullable|date',
        ]);

        // Generate sequential WO number
        $nextId = (WorkOrder::max('id') ?? 0) + 1;
        $data['wo_number'] = 'WO-' . str_pad((string)$nextId, 6, '0', STR_PAD_LEFT);

        $data['created_by']   = Auth::id();
        $data['requested_by'] = Auth::id();

        WorkOrder::create($data);

        return redirect()
            ->route('work-orders.index')
            ->with('success', 'Work order created.');
    }

        public function show(WorkOrder $workOrder)
    {
        $auditLogs = AuditLog::query()
            ->with('causer')
            ->where('auditable_type', $workOrder->getMorphClass())
            ->where('auditable_id', $workOrder->id)
            ->latest()
            ->get();

        return view('work_orders.show', compact('workOrder', 'auditLogs'));
    }

    public function edit(WorkOrder $workOrder)
    {
        return view('work_orders.edit', compact('workOrder'));
    }

    public function update(Request $request, WorkOrder $workOrder)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:255',
            'priority'    => 'required|string|max:50',
            'status'      => 'required|string|max:50',
            'due_date'    => 'nullable|date',
        ]);

        $data['updated_by'] = Auth::id();

        $workOrder->update($data);

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with('success', 'Work order updated.');
    }

    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()
            ->route('work-orders.index')
            ->with('success', 'Work order deleted.');
    }

    public function print(WorkOrder $workOrder)
    {
        return view('print.work_order', compact('workOrder'));
    }

    public function pdf(WorkOrder $workOrder)
    {
        $fileName = ($workOrder->wo_number ?? 'WO') . '.pdf';

        $pdf = Pdf::loadView('pdf.work_order', compact('workOrder'))
            ->setPaper('letter', 'portrait');

        return $pdf->download($fileName);
    }
}
