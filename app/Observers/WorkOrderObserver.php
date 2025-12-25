<?php

namespace App\Observers;

use App\Models\WorkOrder;
use App\Support\Audit;

class WorkOrderObserver
{
    public function created(WorkOrder $wo): void
    {
        Audit::log($wo, 'created', [
            'new' => $wo->getAttributes(),
        ]);
    }

    /**
     * Log only what actually changed AFTER persistence.
     * This avoids logging changes that never commit.
     */
    public function updated(WorkOrder $wo): void
    {
        $changes = $wo->getChanges(); // keys that were persisted
        if (empty($changes)) return;

        // Ignore timestamp-only noise
        if (count($changes) === 1 && array_key_exists('updated_at', $changes)) {
            return;
        }

        $old = [];
        foreach (array_keys($changes) as $k) {
            $old[$k] = $wo->getOriginal($k);
        }

        $event = array_key_exists('status', $changes) ? 'status_changed' : 'updated';

        Audit::log($wo, $event, [
            'old'   => $old,
            'new'   => $changes,
            'dirty' => array_keys($changes),
        ]);
    }

    public function deleted(WorkOrder $wo): void
    {
        Audit::log($wo, 'deleted', [
            'old' => $wo->getOriginal(),
        ]);
    }

    /**
     * Optional: capture "hard deletes" vs soft deletes if you ever add soft deletes.
     */
    public function forceDeleted(WorkOrder $wo): void
    {
        Audit::log($wo, 'force_deleted', [
            'old' => $wo->getOriginal(),
        ]);
    }
}
