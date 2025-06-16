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
    ];

    protected $casts = [
        'sacraments_received' => 'array',
        'formations_received' => 'array',
        'date_of_birth' => 'date',
    ];
    protected $appends = ['profile_picture_url'];

    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture ?:
            'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($this->detail->full_name);
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

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('attendance_status', 'checked_in_at')
            ->withTimestamps();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
