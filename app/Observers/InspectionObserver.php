<?php

namespace App\Observers;

use App\Models\AuditLog;
use App\Models\Inspection;
use Illuminate\Support\Facades\Auth;

class InspectionObserver
{
    public function created(Inspection $inspection): void
    {
        $this->log('created', $inspection, null, $inspection->getAttributes());
    }

    public function updated(Inspection $inspection): void
    {
        $dirty = $inspection->getChanges(); // only what changed
        if (empty($dirty)) return;

        $old = [];
        foreach (array_keys($dirty) as $k) {
            $old[$k] = $inspection->getOriginal($k);
        }

        $this->log('updated', $inspection, $old, $dirty);
    }

    public function deleted(Inspection $inspection): void
    {
        $this->log('deleted', $inspection, $inspection->getOriginal(), null);
    }

    /**
     * Use this for explicit events like "certified" / "unlocked"
     * from controllers/services.
     */
    public static function logEvent(string $event, Inspection $inspection, array $meta = []): void
    {
        AuditLog::create([
            'event' => $event,

            // ✅ MUST match DB columns:
            'auditable_type' => $inspection->getMorphClass(),
            'auditable_id'   => $inspection->id,

            'causer_id' => Auth::id(),
            'properties' => ['meta' => $meta],

            // ✅ request() helper works anywhere in HTTP lifecycle
            'ip' => request()->ip(),
            'user_agent' => (string) request()->userAgent(),
        ]);
    }

    private function log(string $event, Inspection $inspection, $old, $new): void
    {
        $ip = null;
        $ua = null;

        if (app()->runningInConsole() === false) {
            $ip = request()->ip();
            $ua = (string) request()->userAgent();
        }

        AuditLog::create([
            'event' => $event,
            'auditable_type' => $inspection->getMorphClass(),
            'auditable_id'   => $inspection->id,
            'causer_id' => Auth::id(),
            'properties' => ['old' => $old, 'new' => $new],
            'ip' => $ip,
            'user_agent' => $ua,
        ]);
    }
}
