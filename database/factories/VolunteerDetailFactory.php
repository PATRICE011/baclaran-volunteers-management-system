<?php

namespace Database\Factories;

use App\Models\Volunteer;
use App\Models\Ministry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class VolunteerDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'volunteer_id' => Volunteer::factory(),
            'ministry_id' => Ministry::whereNotNull('parent_id')->inRandomOrder()->value('id'),
            'line_group' => Arr::random(['RMM', 'RYM', 'RCCOM']),
            'applied_month_year' => $this->faker->date('Y-m'),
            'regular_years_month' => $this->faker->date('F Y'),
            'full_name' => $this->faker->name,
            'volunteer_status' => Arr::random(['Active', 'Inactive', 'On-Leave']),
        ];
    }
}