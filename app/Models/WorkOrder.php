<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'wo_number',
        'title',
        'description',
        'location',
        'priority',
        'status',
        'due_date',
        'created_by',
        'requested_by',
        'updated_by',
        'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    // Relations if/when we want them later:
    // public function creator()
    // {
    //     return $this->belongsTo(User::class, 'created_by');
    // }

    // public function requester()
    // {
    //     return $this->belongsTo(User::class, 'requested_by');
    // }

    // public function updater()
    // {
    //     return $this->belongsTo(User::class, 'updated_by');
    // }
}
