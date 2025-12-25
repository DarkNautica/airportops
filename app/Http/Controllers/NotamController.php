<?php

namespace App\Http\Controllers;

use App\Models\Notam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotamController extends Controller
{
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'station'        => ['nullable','string','max:8'],
            'subject'        => ['nullable','string','max:255'],
            'category'       => ['required','string'],
            'status'         => ['required','string'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'notam_text'     => ['required','string'],
        ]);

        $data['station'] = $data['station'] ?: 'KAVL';

        // Generate NOTAM number (simple + reliable)
        $next = (Notam::max('id') ?? 0) + 1;
        $data['notam_number'] = 'NOTAM-' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);

        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $notam = Notam::create($data);

        // If you want audit logging like Inspections:
        // \App\Observers\NotamObserver::logEvent('created', $notam);

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

    public function update(Request $request, Notam $notam)
    {
        if ($notam->is_locked) {
            return redirect()->route('notams.show', $notam)->with('error', 'This NOTAM is locked and cannot be updated.');
        }

        $data = $request->validate([
            'station'        => ['nullable','string','max:8'],
            'subject'        => ['nullable','string','max:255'],
            'category'       => ['required','string'],
            'status'         => ['required','string'],
            'effective_from' => ['nullable','date'],
            'effective_to'   => ['nullable','date','after_or_equal:effective_from'],
            'notam_text'     => ['required','string'],
        ]);

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
        // If you want admin-only: gate/policy here
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
        // If you want admin-only: gate/policy here
        $notam->update([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
            'updated_by'=> Auth::id(),
        ]);

        return redirect()->route('notams.show', $notam)->with('success', 'NOTAM unlocked.');
    }
}
