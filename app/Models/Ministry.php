<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ministry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ministry_name',
        'parent_id',
        'ministry_type',
        'max_members',
        'required_attendance_per_month',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the parent ministry that this ministry belongs to.
     */
    // In Ministry.php
    // In Ministry.php
    public function volunteers()
    {
        return $this->belongsToMany(Volunteer::class, 'volunteer_details', 'ministry_id', 'volunteer_id')
            ->where('is_archived', false);
    }

    // Remove or modify the volunteerDetails() relationship if it's not needed
    public function volunteerDetails()
    {
        return $this->hasMany(VolunteerDetail::class, 'ministry_id')
            ->whereHas('volunteer', function ($q) {
                $q->where('is_archived', false);
            });
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Ministry::class, 'parent_id');
    }

    /**
     * Get all child ministries under this ministry.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Ministry::class, 'parent_id');
    }


    public function scopeMainMinistries($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope a query to only include ministries of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('ministry_type', $type);
    }

    /**
     * Get all ministries with their children recursively.
     */
    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }

    /**
     * Check if ministry has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Get the full hierarchy path of the ministry.
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->ministry_name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->ministry_name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }
    public function getTotalVolunteersAttribute()
    {
        if ($this->hasChildren()) {
            return $this->children()->withCount('volunteerDetails')->get()->sum('volunteer_details_count');
        }
        return $this->volunteerDetails()->count();
    }
}
