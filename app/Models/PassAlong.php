<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PassAlong extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'specialist_name',
        'shift_start_time',
        'shift_end_time',
        'am_part_139',
        'am_perimeter',
        'am_terminal',
        'pm_part_139',
        'pm_terminal',
        'ramp_apron_patrol',
        'wildlife_patrol',
        'significant_activity',
        'sections',
        'status',
        'is_locked',
        'submitted_at',
        'submitted_by',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'date' => 'date',
        'submitted_at' => 'datetime',
        'locked_at' => 'datetime',

        'am_part_139' => 'boolean',
        'am_perimeter' => 'boolean',
        'am_terminal' => 'boolean',
        'pm_part_139' => 'boolean',
        'pm_terminal' => 'boolean',
        'ramp_apron_patrol' => 'boolean',
        'wildlife_patrol' => 'boolean',

        'is_locked' => 'boolean',
        'sections' => 'array',
    ];

    /**
     * Policy expects this. This is your single source of truth for "locked/immutable".
     */
    public function isImmutable(): bool
    {
        return (bool) $this->is_locked || $this->status === 'submitted';
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(PassAlongRecipient::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(PassAlongAttachment::class);
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }
}
