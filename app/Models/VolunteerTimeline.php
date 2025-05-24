<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class VolunteerTimeline extends Model
{
     use HasFactory;
    protected $fillable = [
        'volunteer_id', 'organization_name',
        'year_started', 'year_ended', 'total_years', 'is_active',
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }
}
