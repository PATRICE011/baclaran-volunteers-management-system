<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    protected $fillable = [
        'title',
        'date',
        'start_time',
        'end_time',
        'location',
        'description',
        'color',
    ];
    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];
    public function volunteers(): BelongsToMany
    {
        return $this->belongsToMany(Volunteer::class)
            ->withPivot('attendance_status', 'checked_in_at')
            ->withTimestamps();
    }
}
