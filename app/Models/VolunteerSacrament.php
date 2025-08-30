<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VolunteerSacrament extends Model
{
    use HasFactory;

    protected $fillable = [
        'volunteer_id',
        'sacrament_name',
        // 'year'
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
}