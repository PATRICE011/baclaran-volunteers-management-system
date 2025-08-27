<?php

namespace Database\Factories;

use App\Models\Volunteer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class VolunteerFactory extends Factory
{
    protected $model = Volunteer::class;

    public function definition(): array
    {
        return [
            'volunteer_id' => 'VOL-' . Str::upper(Str::random(8)),
            'nickname' => $this->faker->firstName,
            'date_of_birth' => $this->faker->date(),
            'sex' => Arr::random(['Male', 'Female']),
            'address' => $this->faker->address,
            'mobile_number' => $this->faker->phoneNumber,
            'email_address' => $this->faker->unique()->safeEmail,
            'occupation' => $this->faker->jobTitle,
            'civil_status' => Arr::random(['Single', 'Married', 'Widow/er', 'Separated', 'Church', 'Civil', 'Others']),
            'profile_picture' => null,
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => User::factory(),
            'archive_reason' => $this->faker->sentence(),
        ]);
    }
}