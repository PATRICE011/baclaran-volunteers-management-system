<?php

namespace Database\Factories;

use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use App\Models\Ministry;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VolunteerDetail>
 */
class VolunteerDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            // No need to generate new volunteer_id here!
            'volunteer_id' => null,
            'ministry_id' => Ministry::whereNotNull('parent_id')->inRandomOrder()->value('id'),
            'line_group' => Arr::random(['RMM', 'RYM', 'RCCOM']),
            'applied_month_year' => $this->faker->date('F Y'),
            'regular_years_month' => $this->faker->date('F Y'),
            'full_name' => $this->faker->name,
            'volunteer_status' => Arr::random(['Active', 'Inactive']),
        ];
    }
}
