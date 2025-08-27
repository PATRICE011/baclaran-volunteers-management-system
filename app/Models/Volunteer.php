<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Volunteer extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'nickname',
        'date_of_birth',
        'sex',
        'address',
        'mobile_number',
        'email_address',
        'occupation',
        'civil_status',
        'profile_picture',
        'is_archived',
        'archived_at',
        'archived_by',
        'archive_reason'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'archived_at' => 'datetime',
    ];

    protected $appends = ['profile_picture_url'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($volunteer) {
            if (empty($volunteer->volunteer_id)) {
                $volunteer->volunteer_id = 'VOL-' . strtoupper(Str::random(6));
            }
        });
    }

    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }

        // Use the full_name from the detail relationship if available
        $name = $this->detail ? $this->detail->full_name : ($this->nickname ?? 'default');
        return 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($name);
    }

    // Relationships
    public function detail(): HasOne
    {
        return $this->hasOne(VolunteerDetail::class);
    }

    public function sacraments(): HasMany
    {
        return $this->hasMany(VolunteerSacrament::class);
    }

    public function formations(): HasMany
    {
        return $this->hasMany(VolunteerFormation::class);
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

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_volunteer')
            ->withPivot('attendance_status', 'checked_in_at')
            ->withTimestamps();
    }

    public function archiver()
    {
        return $this->belongsTo(User::class, 'archived_by')->withDefault([
            'full_name' => 'System'
        ]);
    }

    // Accessors
    public function getMinistryNameAttribute()
    {
        return $this->detail?->ministry?->ministry_name ?? 'No Ministry Assigned';
    }

    // Methods
    public function hasCompleteProfile(): bool
    {
        return $this->detail !== null;
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
}