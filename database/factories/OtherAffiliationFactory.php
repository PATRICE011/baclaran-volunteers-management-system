<?php

namespace Database\Factories;

use App\Models\OtherAffiliation;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtherAffiliationFactory extends Factory
{
    protected $model = OtherAffiliation::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        $organizations = [
            'Red Cross',
            'Habitat for Humanity',
            'Local Food Bank',
            'Environmental Group',
            'Community Center',
            'Educational Foundation',
            'Healthcare Volunteers',
            'Disaster Relief Organization'
        ];

        $yearStarted = $this->faker->numberBetween(2015, 2023);
        $yearEnded = $this->faker->optional(0.4)->numberBetween($yearStarted + 1, 2025);
        $isActive = is_null($yearEnded);

        return [
            'volunteer_id' => Volunteer::factory(),
            'organization_name' => $this->faker->randomElement($organizations),
            'year_started' => (string) $yearStarted,
            'year_ended' => $yearEnded ? (string) $yearEnded : null,
            'is_active' => $isActive,
        ];
    }
}