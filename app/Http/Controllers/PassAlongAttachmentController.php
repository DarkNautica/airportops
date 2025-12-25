<?php

namespace App\Http\Controllers;

use App\Models\PassAlong;
use App\Models\PassAlongAttachment;
use App\Support\Audit;
use Illuminate\Support\Facades\Storage;

class PassAlongAttachmentController extends Controller
{
    public function download(PassAlong $pass_along, PassAlongAttachment $attachment)
    {
        $this->authorize('view', $pass_along);

        abort_unless((int)$attachment->pass_along_id === (int)$pass_along->id, 404);

        $disk = $attachment->disk ?: 'public';

        Audit::log($pass_along, 'attachment_downloaded', [
            'attachment_id' => $attachment->id,
            'filename'      => $attachment->original_name,
            'path'          => $attachment->path,
            'disk'          => $disk,
        ]);

        if (!Storage::disk($disk)->exists($attachment->path)) {
            abort(404, 'File not found on disk.');
        }

        $downloadName = $attachment->original_name ?: basename($attachment->path);

        // Local/Public disks: use absolute path + response()->download (reliable)
        try {
            $absolutePath = Storage::disk($disk)->path($attachment->path);
            if (is_file($absolutePath)) {
                return response()->download($absolutePath, $downloadName);
            }
        } catch (\Throwable $e) {
            // fall through to streaming method
        }

        // Fallback: stream download (works across drivers)
        $stream = Storage::disk($disk)->readStream($attachment->path);

        return response()->streamDownload(function () use ($stream) {
            if (is_resource($stream)) {
                fpassthru($stream);
                fclose($stream);
            }
        }, $downloadName);
    }
}
