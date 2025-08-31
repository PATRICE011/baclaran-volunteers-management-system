<?php

namespace Database\Factories;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VolunteerFactory extends Factory
{
    protected $model = Volunteer::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        return [
            'volunteer_id' => 'VOL-' . strtoupper(Str::random(6)),
            'nickname' => $this->faker->firstName(),
            'date_of_birth' => $this->faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'sex' => $this->faker->randomElement(['Male', 'Female']),
            'address' => $this->faker->address(),
            'mobile_number' => $this->faker->phoneNumber(),
            'email_address' => $this->faker->unique()->safeEmail(),
            'occupation' => $this->faker->jobTitle(),
            'civil_status' => $this->faker->randomElement(['Single', 'Married', 'Widow/er', 'Separated', 'Church', 'Civil', 'Others']),
            'profile_picture' => null,
            'is_archived' => false,
        ];
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_archived' => true,
            'archived_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'archive_reason' => $this->faker->sentence(),
        ]);
    }
}
