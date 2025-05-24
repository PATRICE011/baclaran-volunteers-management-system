<?php

namespace Database\Factories;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VolunteerTimeline>
 */
class VolunteerTimelineFactory extends Factory
{
    public function definition(): array
    {
        $startYear = $this->faker->year();
        $endYear = $this->faker->boolean(70) ? $this->faker->numberBetween($startYear, now()->year) : null;
        $totalYears = $endYear ? $endYear - $startYear : null;

        return [
            'volunteer_id' => null, // ✅ set in configure()
            'organization_name' => $this->faker->company,
            'year_started' => $startYear,
            'year_ended' => $endYear,
            'total_years' => $totalYears,
            'is_active' => $endYear === null,
        ];
    }
}
