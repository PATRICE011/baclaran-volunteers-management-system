<?php

namespace Database\Factories;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class VolunteerSacramentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'volunteer_id' => Volunteer::factory(),
            'sacrament_name' => Arr::random(['Baptism', 'First Communion', 'Confirmation', 'Marriage', 'Holy Orders']),
            'year' => $this->faker->year(),
        ];
    }
}