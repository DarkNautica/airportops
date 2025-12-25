<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notam extends Model
{
    protected $fillable = [
        'notam_number',
        'station',
        'subject',
        'category',
        'status',
        'effective_from',
        'effective_to',
        'notam_text',
        'meta',
        'created_by',
        'updated_by',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'effective_from' => 'datetime',
        'effective_to'   => 'datetime',
        'meta'           => 'array',
        'is_locked'      => 'boolean',
        'locked_at'      => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    // Optional: mirror inspections style if you want audit logs here too
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}
