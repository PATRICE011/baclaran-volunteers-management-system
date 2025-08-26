<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
        'ministry_id',
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_reason'
    ];


    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }
    protected $casts = [
        'due_date' => 'date:Y-m-d',
        'archived_at' => 'datetime',
    ];
    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }
    // In your Task model
    public function archive($reason)
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
            'archive_reason' => $reason
        ]);
    }
    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by')->withDefault([
            'full_name' => 'System'
        ]);
    }
    public function restore()
    {
        $this->update([
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null
        ]);
    }
}
