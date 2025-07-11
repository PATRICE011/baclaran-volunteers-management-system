<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Volunteer extends Model
{
    use HasFactory;
    protected $fillable = [
        'nickname',
        'date_of_birth',
        'sex',
        'address',
        'mobile_number',
        'email_address',
        'occupation',
        'civil_status',
        'sacraments_received',
        'formations_received',
        'profile_picture',
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_reason'
    ];
    protected $casts = [
        'sacraments_received' => 'array',
        'formations_received' => 'array',
        'date_of_birth' => 'date',
        'archived_at' => 'datetime',
    ];
    protected $appends = ['profile_picture_url'];

    // Volunteer.php (getProfilePictureUrlAttribute)
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        return 'https://api.dicebear.com/7.x/avataaars/svg?seed=' .
            urlencode($this->detail->full_name ?? 'default');
    }
    public function detail()
    {
        return $this->hasOne(VolunteerDetail::class);
    }
    public function getMinistryNameAttribute()
    {
        return $this->detail?->ministry?->ministry_name ?? 'No Ministry Assigned';
    }
    public function hasCompleteProfile(): bool
    {
        return $this->detail !== null;
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(VolunteerTimeline::class);
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(OtherAffiliation::class);
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by')->withDefault([
            'full_name' => 'System'
        ]);
    }

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
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_volunteer')
            ->withPivot('attendance_status', 'checked_in_at')
            ->withTimestamps();
    }
}
