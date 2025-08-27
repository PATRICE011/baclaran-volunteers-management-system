<?php

namespace Database\Factories;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtherAffiliationFactory extends Factory
{
    public function definition(): array
    {
        $startYear = $this->faker->year();
        $endYear = $this->faker->boolean(70) ? $this->faker->numberBetween($startYear, now()->year) : null;

        return [
            'volunteer_id' => Volunteer::factory(),
            'organization_name' => $this->faker->company,
            'year_started' => $startYear,
            'year_ended' => $endYear,
            'is_active' => $endYear === null,
        ];
    }
}