<?php

namespace Database\Factories;

use App\Models\VolunteerTimeline;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerTimelineFactory extends Factory
{
    protected $model = VolunteerTimeline::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        $organizations = [
            'Parish Youth Group',
            'Knights of Columbus',
            'Legion of Mary',
            'Couples for Christ',
            'Bible Study Group',
            'Prayer Group',
            'Community Outreach',
            'Local NGO'
        ];

        $yearStarted = $this->faker->numberBetween(2010, 2023);
        $yearEnded = $this->faker->optional(0.3)->numberBetween($yearStarted + 1, 2025);
        $isActive = is_null($yearEnded);
        $totalYears = $yearEnded ? ($yearEnded - $yearStarted) : (2025 - $yearStarted);

        return [
            'volunteer_id' => Volunteer::factory(),
            'organization_name' => $this->faker->randomElement($organizations),
            'year_started' => (string) $yearStarted,
            'year_ended' => $yearEnded ? (string) $yearEnded : null,
            'total_years' => $totalYears,
            'is_active' => $isActive,
        ];
    }
}