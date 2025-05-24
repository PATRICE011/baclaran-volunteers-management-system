<?php

namespace Database\Factories;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OtherAffiliation>
 */
class OtherAffiliationFactory extends Factory
{
   public function definition(): array
{
    $startYear = $this->faker->year;
    return [
        'volunteer_id' => null, // ✅ let the parent factory assign it
        'organization_name' => $this->faker->company,
        'year_started' => $startYear,
        'year_ended' => $this->faker->optional()->year,
        'is_active' => $this->faker->boolean,
    ];
}

}
