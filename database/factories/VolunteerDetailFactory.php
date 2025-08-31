<?php

namespace Database\Factories;

use App\Models\VolunteerDetail;
use App\Models\Volunteer;
use App\Models\Ministry;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerDetailFactory extends Factory
{
    protected $model = VolunteerDetail::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        $appliedDate = $this->faker->dateTimeBetween('-10 years', '-1 year');
        $regularDate = $this->faker->dateTimeBetween($appliedDate, 'now');

        return [
            'volunteer_id' => Volunteer::factory(),
            'ministry_id' => function () {
                // Get a random ministry that has a parent (actual ministry, not category)
                return Ministry::whereNotNull('parent_id')->inRandomOrder()->first()->id ?? 1;
            },
            'line_group' => $this->faker->randomElement(['Group A', 'Group B', 'Group C', 'Special Group']),
            'applied_month_year' => $appliedDate->format('F Y'),
            'regular_years_month' => $regularDate->format('F Y'),
            'full_name' => $this->faker->name(),
            'volunteer_status' => $this->faker->randomElement(['Active', 'Inactive', 'On-Leave']),
        ];
    }
}