<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'insp_number',
        'inspection_date',
        'inspection_time',
        'inspection_type',
        'surface',
        'conditions',
        'findings',
        'status',
        'inspector_id',

        'header',
        'checklist',

        // lock/cert
        'is_locked',
        'certified_at',
        'certified_by',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'inspection_time' => 'datetime:H:i',

        'header' => 'array',
        'checklist' => 'array',

        'is_locked' => 'boolean',
        'certified_at' => 'datetime',
        'locked_at' => 'datetime',
    ];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function certifiedBy()
    {
        return $this->belongsTo(User::class, 'certified_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function auditLogs()
    {
        return $this->morphMany(\App\Models\AuditLog::class, 'auditable')->latest();
    }

}
