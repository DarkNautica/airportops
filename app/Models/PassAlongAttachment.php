<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PassAlongAttachment extends Model
{
    protected $fillable = [
        'pass_along_id',
        'disk',
        'path',
        'original_name',
        'size',
        'mime',
        'uploaded_by',
    ];

    public function passAlong(): BelongsTo
    {
        return $this->belongsTo(PassAlong::class, 'pass_along_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
