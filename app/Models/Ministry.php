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
        'ministry_code',
        'parent_id',
        'ministry_type',
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

    /**
     * Get all volunteer details for this ministry.
     */
    public function volunteerDetails(): HasMany
    {
        return $this->hasMany(VolunteerDetail::class);
    }

    /**
     * Scope a query to only include main ministries (no parent).
     */
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
}