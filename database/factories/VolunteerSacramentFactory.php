<?php

namespace Database\Factories;

use App\Models\VolunteerSacrament;
use App\Models\Volunteer;
use Illuminate\Database\Eloquent\Factories\Factory;

class VolunteerSacramentFactory extends Factory
{
    protected $model = VolunteerSacrament::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        $sacraments = [
            'Baptism',
            'Confirmation',
            'First Communion',
            'Marriage',
            'Holy Orders'
        ];

        return [
            'volunteer_id' => Volunteer::factory(),
            'sacrament_name' => $this->faker->randomElement($sacraments),
        ];
    }
}
