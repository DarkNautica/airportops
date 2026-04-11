<?php

namespace App\Observers;

use App\Models\Notam;
use App\Support\Audit;

class NotamObserver
{
    public function created(Notam $notam): void
    {
        Audit::log($notam, 'created', [
            'new' => $notam->getAttributes(),
        ]);
    }

    public function updated(Notam $notam): void
    {
        $changes = $notam->getChanges();
        if (empty($changes)) return;

        if (count($changes) === 1 && array_key_exists('updated_at', $changes)) {
            return;
        }

        $old = [];
        foreach (array_keys($changes) as $k) {
            $old[$k] = $notam->getOriginal($k);
        }

        // Determine specific event type
        $event = 'updated';
        if (array_key_exists('status', $changes)) {
            $status = $changes['status'] ?? '';
            if ($status === 'Active') {
                $event = 'activated';
            } elseif ($status === 'Cancelled') {
                $event = 'cancelled';
            } else {
                $event = 'status_changed';
            }
        } elseif (array_key_exists('is_locked', $changes)) {
            $event = $changes['is_locked'] ? 'locked' : 'unlocked';
        }

        Audit::log($notam, $event, [
            'old'   => $old,
            'new'   => $changes,
            'dirty' => array_keys($changes),
        ]);
    }

    public function deleted(Notam $notam): void
    {
        Audit::log($notam, 'deleted', [
            'old' => $notam->getOriginal(),
        ]);
    }
}
