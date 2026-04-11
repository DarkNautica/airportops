<?php

namespace App\Observers;

use App\Models\PassAlong;
use App\Support\Audit;
use RuntimeException;

class PassAlongObserver
{
    public function created(PassAlong $passAlong): void
    {
        Audit::log($passAlong, 'created', [
            'new' => $passAlong->getAttributes(),
        ]);
    }

    public function updating(PassAlong $passAlong): void
    {
        // Immutability guard — forceFill() in submit/unlock bypasses this
        // because those methods call save() which triggers updating, but
        // the policy already gates access. We only block if the record
        // was already locked AND the is_locked field is not being changed
        // (i.e. this is not an unlock/submit operation).
        if ($passAlong->isImmutable() && !$passAlong->isDirty('is_locked') && !$passAlong->isDirty('status')) {
            throw new RuntimeException('Pass Along is locked/submitted and cannot be modified.');
        }
    }

    public function updated(PassAlong $passAlong): void
    {
        $changes = $passAlong->getChanges();
        if (empty($changes)) return;

        if (count($changes) === 1 && array_key_exists('updated_at', $changes)) {
            return;
        }

        $old = [];
        foreach (array_keys($changes) as $k) {
            $old[$k] = $passAlong->getOriginal($k);
        }

        $event = 'updated';
        if (array_key_exists('status', $changes) && ($changes['status'] ?? '') === 'submitted') {
            $event = 'submitted';
        } elseif (array_key_exists('is_locked', $changes)) {
            $event = $changes['is_locked'] ? 'locked' : 'unlocked';
        }

        Audit::log($passAlong, $event, [
            'old'   => $old,
            'new'   => $changes,
            'dirty' => array_keys($changes),
        ]);
    }

    public function deleting(PassAlong $passAlong): void
    {
        if ($passAlong->isImmutable()) {
            throw new RuntimeException('Pass Along is locked/submitted and cannot be deleted.');
        }
    }

    public function deleted(PassAlong $passAlong): void
    {
        Audit::log($passAlong, 'deleted', [
            'old' => $passAlong->getOriginal(),
        ]);
    }
}
