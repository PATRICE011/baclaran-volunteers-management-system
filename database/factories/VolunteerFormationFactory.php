<?php

namespace Database\Factories;

use App\Models\VolunteerFormation;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerFormationFactory extends Factory
{
    protected $model = VolunteerFormation::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        $formations = [
            'Basic Leadership Training',
            'Advanced Ministry Formation',
            'Spiritual Direction Course',
            'Youth Leadership Program',
            'Music Ministry Training',
            'Liturgical Formation',
            'Evangelization Training'
        ];

        return [
            'volunteer_id' => Volunteer::factory(),
            'formation_name' => $this->faker->randomElement($formations),
            'year' => $this->faker->numberBetween(2015, 2025),
        ];
    }
}