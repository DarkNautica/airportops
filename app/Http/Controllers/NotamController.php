<?php

namespace App\Http\Controllers;

use App\Models\Notam;
use Illuminate\Http\Request;
use App\Http\Requests\StoreNotamRequest;
use App\Http\Requests\UpdateNotamRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotamController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Notam::class, 'notam');
    }

    public function index(Request $request)
    {
        $q        = trim((string) $request->get('q', ''));
        $status   = $request->get('status', 'Active');
        $category = $request->get('category');
        $station  = $request->get('station', 'KAVL');

        $notams = Notam::query()
            ->where('station', $station)
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('notam_number', 'like', "%{$q}%")
                       ->orWhere('subject', 'like', "%{$q}%")
                       ->orWhere('notam_text', 'like', "%{$q}%");
                });
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderByDesc('effective_from')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('notams.index', compact('notams', 'q', 'status', 'category', 'station'));
    }

    public function create()
    {
        return view('notams.create');
    }

    public function store(StoreNotamRequest $request)
    {
        $data = $request->validated();

        $data['station'] = $data['station'] ?: 'KAVL';

        $data['notam_number'] = $this->nextNotamNumber();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $notam = Notam::create($data);

        return redirect()->route('notams.show', $notam)->with('success', 'NOTAM created.');
    }

    public function show(Notam $notam)
    {
        return view('notams.show', compact('notam'));
    }

    public function edit(Notam $notam)
    {
        if ($notam->is_locked) {
            return redirect()->route('notams.show', $notam)->with('error', 'This NOTAM is locked and cannot be edited.');
        }

        return view('notams.edit', compact('notam'));
    }

    public function update(UpdateNotamRequest $request, Notam $notam)
    {
        if ($notam->is_locked) {
            return redirect()->route('notams.show', $notam)->with('error', 'This NOTAM is locked and cannot be updated.');
        }

        $data = $request->validated();

        $data['station'] = $data['station'] ?: $notam->station;
        $data['updated_by'] = Auth::id();

        $notam->update($data);

        return redirect()->route('notams.show', $notam)->with('success', 'NOTAM updated.');
    }

    public function destroy(Notam $notam)
    {
        if ($notam->is_locked) {
            return redirect()->route('notams.show', $notam)->with('error', 'This NOTAM is locked and cannot be deleted.');
        }

        $notam->delete();

        return redirect()->route('notams.index')->with('success', 'NOTAM deleted.');
    }

    // ---------- workflow actions ----------

    public function activate(Notam $notam)
    {
        $this->authorize('activate', $notam);

        if ($notam->is_locked) {
            return redirect()->route('notams.show', $notam)->with('error', 'Locked NOTAM cannot be activated.');
        }

        $notam->update([
            'status'     => 'Active',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('notams.show', $notam)->with('success', 'NOTAM set to Active.');
    }

    public function cancel(Notam $notam)
    {
        $this->authorize('cancel', $notam);

        if ($notam->is_locked) {
            return redirect()->route('notams.show', $notam)->with('error', 'Locked NOTAM cannot be cancelled.');
        }

        $notam->update([
            'status'     => 'Cancelled',
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('notams.show', $notam)->with('success', 'NOTAM cancelled.');
    }

    public function lock(Notam $notam)
    {
        $this->authorize('lock', $notam);

        $notam->update([
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => Auth::id(),
            'updated_by'=> Auth::id(),
        ]);

        return redirect()->route('notams.show', $notam)->with('success', 'NOTAM locked.');
    }

    public function unlock(Notam $notam)
    {
        $this->authorize('unlock', $notam);

        $notam->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
            'updated_by'=> Auth::id(),
        ]);

        return redirect()->route('notams.show', $notam)->with('success', 'NOTAM unlocked.');
    }

    // -----------------------------
    // Internal helpers
    // -----------------------------

    private function nextNotamNumber(): string
    {
        return DB::transaction(function () {
            $row = DB::table('counters')
                ->where('key', 'notams.notam_number')
                ->lockForUpdate()
                ->first();

            if (!$row) {
                DB::table('counters')->insert([
                    'key' => 'notams.notam_number',
                    'value' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $row = (object) ['value' => 0];
            }

            $next = ((int) $row->value) + 1;

            DB::table('counters')
                ->where('key', 'notams.notam_number')
                ->update([
                    'value' => $next,
                    'updated_at' => now(),
                ]);

            return 'NOTAM-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
        }, 3);
    }
}
