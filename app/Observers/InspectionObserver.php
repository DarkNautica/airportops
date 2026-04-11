<?php

namespace App\Observers;

use App\Models\Inspection;
use App\Support\Audit;
use Illuminate\Support\Facades\Auth;

class InspectionObserver
{
    public function created(Inspection $inspection): void
    {
        Audit::log($inspection, 'created', [
            'new' => $inspection->getAttributes(),
        ]);
    }

    public function updated(Inspection $inspection): void
    {
        $changes = $inspection->getChanges();
        if (empty($changes)) return;

        if (count($changes) === 1 && array_key_exists('updated_at', $changes)) {
            return;
        }

        $old = [];
        foreach (array_keys($changes) as $k) {
            $old[$k] = $inspection->getOriginal($k);
        }

        $event = array_key_exists('status', $changes) ? 'status_changed' : 'updated';

        Audit::log($inspection, $event, [
            'old'   => $old,
            'new'   => $changes,
            'dirty' => array_keys($changes),
        ]);
    }

    public function deleted(Inspection $inspection): void
    {
        Audit::log($inspection, 'deleted', [
            'old' => $inspection->getOriginal(),
        ]);
    }

    /**
     * Use this for explicit events like "certified" / "unlocked"
     * from controllers/services.
     */
    public static function logEvent(string $event, Inspection $inspection, array $meta = []): void
    {
        Audit::log($inspection, $event, ['meta' => $meta]);
    }
}
