<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'event',
        'auditable_type',
        'auditable_id',
        'causer_id',
        'properties',
        'ip',
        'user_agent',
        'prev_hash',
        'hash',
        'hash_algo',
        'hash_version',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    protected static function booted()
    {
        static::updating(function () {
            throw new \RuntimeException('Audit logs are immutable.');
        });

        static::deleting(function () {
            throw new \RuntimeException('Audit logs are immutable.');
        });
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function causer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
