<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VolunteerDetail extends Model
{
    protected $fillable = [
        'volunteer_id', 'ministry_id', 'line_group',
        'applied_month_year', 'regular_years_month', 'full_name',
    ];

    public function volunteer(): BelongsTo
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }
}
