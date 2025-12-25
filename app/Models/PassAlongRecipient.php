<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassAlongRecipient extends Model
{
    protected $fillable = [
        'pass_along_id','user_id','name','email',
        'queued_at','sent_at','status','message_id','error'
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
    ];

    public function passAlong(): BelongsTo
    {
        return $this->belongsTo(PassAlong::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
