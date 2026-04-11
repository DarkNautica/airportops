<?php

namespace App\Http\Controllers;

use App\Models\PassAlong;
use App\Models\PassAlongAttachment;
use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PassAlongController extends Controller
{
    public function __construct()
    {
        // MUST match your route param {pass_along}
        $this->authorizeResource(PassAlong::class, 'pass_along');
    }

    public function index()
    {
        $passAlongs = PassAlong::query()
            ->latest('date')
            ->latest('id')
            ->paginate(20);

        return view('pass_alongs.index', compact('passAlongs'));
    }

    public function create()
    {
        $defaultSections = [
            ['title' => 'Pass Along', 'rows' => array_fill(0, 18, ['label' => '', 'value' => ''])],
            ['title' => 'Additional Notes', 'rows' => array_fill(0, 18, ['label' => '', 'value' => ''])],
        ];

        return view('pass_alongs.create', compact('defaultSections'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $passAlong = PassAlong::create($data);

        // ✅ SAVE row_attachments[*][*][]
        $this->storeRowAttachments($request, $passAlong);

        Audit::log($passAlong, 'created', [
            'summary' => 'Pass Along created',
        ]);

        return redirect()->route('pass-alongs.show', $passAlong)
            ->with('success', 'Pass Along saved.');
    }

    public function show(PassAlong $pass_along)
    {
        // ✅ load attachments for your show page
        $pass_along->load(['recipients', 'attachments']);

        return view('pass_alongs.show', ['passAlong' => $pass_along]);
    }

    public function edit(PassAlong $pass_along)
    {
        $pass_along->load(['attachments']);

        return view('pass_alongs.edit', ['passAlong' => $pass_along]);
    }

    public function update(Request $request, PassAlong $pass_along)
    {
        $data = $this->validated($request);

        $before = $pass_along->getOriginal();

        $pass_along->update($data);

        // ✅ SAVE row_attachments[*][*][]
        $this->storeRowAttachments($request, $pass_along);

        $after = $pass_along->fresh()->toArray();

        Audit::log($pass_along, 'updated', [
            'summary' => 'Pass Along updated',
            'changes' => [
                'before' => $before,
                'after'  => $after,
            ],
        ]);

        return redirect()->route('pass-alongs.show', $pass_along)
            ->with('success', 'Changes saved.');
    }

    /**
     * Optional generic upload endpoint (your routes already include it).
     * Keep it for later. Your main saving path is storeRowAttachments().
     */
    public function uploadAttachment(Request $request, PassAlong $pass_along)
    {
        $this->authorize('update', $pass_along);

        $request->validate([
            'attachments'   => ['required', 'array'],
            'attachments.*' => ['file', 'max:12288', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store("pass_alongs/{$pass_along->id}", 'public');

            PassAlongAttachment::create([
                'pass_along_id'   => $pass_along->id,
                'disk'            => 'public',
                'path'            => $path,
                'original_name'   => $file->getClientOriginalName(),
                'size'            => $file->getSize(),
                'mime'            => $file->getMimeType(),
                'uploaded_by'     => Auth::id(),
            ]);
        }

        Audit::log($pass_along, 'attachment_uploaded', [
            'summary' => 'Attachment(s) uploaded',
        ]);

        return back()->with('success', 'Attachment(s) uploaded.');
    }

    public function deleteAttachment(PassAlong $pass_along, PassAlongAttachment $attachment)
    {
        $this->authorize('update', $pass_along);

        abort_unless($attachment->pass_along_id === $pass_along->id, 404);

        $disk = $attachment->disk ?: 'public';
        if (Storage::disk($disk)->exists($attachment->path)) {
            Storage::disk($disk)->delete($attachment->path);
        }

        $attachment->delete();

        Audit::log($pass_along, 'attachment_deleted', [
            'attachment_id' => $attachment->id,
            'filename'      => $attachment->original_name,
        ]);

        return back()->with('success', 'Attachment deleted.');
    }

    public function submit(Request $request, PassAlong $pass_along)
    {
        $this->authorize('submit', $pass_along);

        $pass_along->forceFill([
            'status'       => 'submitted',
            'is_locked'    => true,
            'submitted_at' => now(),
            'submitted_by' => Auth::id(),
            'locked_at'    => now(),
            'locked_by'    => Auth::id(),
        ])->save();

        Audit::log($pass_along, 'submitted', [
            'summary' => 'Pass Along submitted and locked',
        ]);

        return redirect()->route('pass-alongs.show', $pass_along)
            ->with('success', 'Submitted & locked.');
    }

    public function unlock(PassAlong $pass_along)
    {
        $this->authorize('unlock', $pass_along);

        $pass_along->forceFill([
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
        ])->save();

        Audit::log($pass_along, 'unlocked', [
            'summary' => 'Pass Along unlocked by admin',
        ]);

        return back()->with('success', 'Unlocked.');
    }

    public function print(PassAlong $pass_along)
    {
        $this->authorize('export', $pass_along);

        Audit::log($pass_along, 'printed', [
            'summary' => 'Pass Along print view opened',
        ]);

        $pass_along->load(['attachments']);

        return view('pass_alongs.print', ['passAlong' => $pass_along]);
    }

    public function pdf(PassAlong $pass_along)
    {
        $this->authorize('export', $pass_along);

        Audit::log($pass_along, 'pdf_generated', [
            'summary' => 'Pass Along PDF generated',
        ]);

        $pass_along->load(['attachments']);

        $pdf = Pdf::loadView('pass_alongs.print', ['passAlong' => $pass_along])
            ->setPaper('letter');

        return $pdf->download("pass-along-{$pass_along->id}.pdf");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date'],
            'specialist_name' => ['nullable', 'string', 'max:255'],

            'shift_start_time' => ['nullable', 'date_format:H:i'],
            'shift_end_time'   => ['nullable', 'date_format:H:i'],

            'am_part_139'       => ['sometimes', 'boolean'],
            'am_perimeter'      => ['sometimes', 'boolean'],
            'am_terminal'       => ['sometimes', 'boolean'],
            'pm_part_139'       => ['sometimes', 'boolean'],
            'pm_terminal'       => ['sometimes', 'boolean'],
            'ramp_apron_patrol' => ['sometimes', 'boolean'],
            'wildlife_patrol'   => ['sometimes', 'boolean'],

            'significant_activity' => ['nullable', 'string'],

            'sections' => ['nullable', 'array'],
            'sections.*.title' => ['required_with:sections', 'string'],
            'sections.*.rows' => ['required_with:sections', 'array'],
            'sections.*.rows.*.label' => ['nullable', 'string'],
            'sections.*.rows.*.value' => ['nullable', 'string'],

            // ✅ validate row_attachments[*][*][]
            'row_attachments' => ['nullable', 'array'],
            'row_attachments.*' => ['nullable', 'array'],
            'row_attachments.*.*' => ['nullable', 'array'],
            'row_attachments.*.*.*' => ['file', 'max:12288', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);
    }

    /**
     * This is the missing piece that kept your attachment count at 0.
     * It stores files and creates PassAlongAttachment rows.
     */
    private function storeRowAttachments(Request $request, PassAlong $passAlong): void
    {
        $filesGrid = $request->file('row_attachments', []);
        if (!is_array($filesGrid) || empty($filesGrid)) {
            return;
        }

        foreach ($filesGrid as $sectionIndex => $rows) {
            if (!is_array($rows)) continue;

            foreach ($rows as $rowIndex => $files) {
                if (!is_array($files)) continue;

                foreach ($files as $file) {
                    if (!$file) continue;

                    $path = $file->store("pass_alongs/{$passAlong->id}", 'public');

                    PassAlongAttachment::create([
                        'pass_along_id' => $passAlong->id,
                        'disk'          => 'public',
                        'path'          => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'size'          => $file->getSize(),
                        'mime'          => $file->getMimeType(),
                        'uploaded_by'   => Auth::id(),

                        // NOTE: we are NOT writing section_index/row_index here
                        // because your attachments table migration you pasted does not include those columns yet.
                        // We'll add that later once uploads are confirmed working.
                    ]);
                }
            }
        }
    }
}
