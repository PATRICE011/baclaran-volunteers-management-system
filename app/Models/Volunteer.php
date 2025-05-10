<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Volunteer extends Model
{
    protected $fillable = [
        'nickname', 'date_of_birth', 'sex', 'address',
        'mobile_number', 'email_address', 'occupation', 'civil_status',
        'sacraments_received', 'formations_received',
    ];

    protected $casts = [
        'sacraments_received' => 'array',
        'formations_received' => 'array',
        'date_of_birth' => 'date',
    ];

    public function detail(): HasOne
    {
        return $this->hasOne(VolunteerDetail::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(VolunteerTimeline::class);
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(OtherAffiliation::class);
    }
}

