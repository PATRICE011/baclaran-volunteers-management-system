<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'date',
        'start_time',
        'end_time',
        'ministry_id',
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_reason'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'archived_at' => 'datetime',
    ];
    public function archive($reason)
    {
        $this->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->id(),
            'archive_reason' => $reason
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

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by')->withDefault([
            'full_name' => 'System'
        ]);
    }
    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(Volunteer::class)
            ->withPivot('attendance_status', 'checked_in_at')
            ->withTimestamps();
    }
}
