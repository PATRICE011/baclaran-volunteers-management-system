<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp',
        'purpose',
        'pending_data',
        'expires_at',
        'is_used',
    ];

    protected $casts = [
        'pending_data' => 'array',
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }
}