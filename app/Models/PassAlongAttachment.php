<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PassAlongAttachment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pass_along_id',
        'disk',
        'path',
        'original_name',
        'size',
        'mime',
        'uploaded_by',
        'section_index',
        'row_index',
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
