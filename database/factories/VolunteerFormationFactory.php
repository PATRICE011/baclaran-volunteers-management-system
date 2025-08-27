<?php

namespace Database\Factories;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class VolunteerFormationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'volunteer_id' => Volunteer::factory(),
            'formation_name' => Arr::random(['BOS', 'BFF', 'YES', 'LTS', 'SFC']),
            'year' => $this->faker->year(),
        ];
    }
}