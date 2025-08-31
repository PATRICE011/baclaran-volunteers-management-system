<?php
namespace Database\Factories;

use App\Models\Event;
use App\Models\Ministry;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        $date = $this->faker->dateTimeBetween('now', '+6 months');
        $startTime = $this->faker->time('H:i');
        $endTime = $this->faker->time('H:i', strtotime($startTime . ' +3 hours'));

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'ministry_id' => function () {
                // Get a random ministry that has a parent (actual ministry, not category)
                return Ministry::whereNotNull('parent_id')->inRandomOrder()->first()->id ?? 1;
            },
            'is_archived' => false,
            'pre_registration_deadline' => $this->faker->optional(0.7)->dateTimeBetween('now', $date),
            'allow_pre_registration' => $this->faker->boolean(70),
        ];
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_archived' => true,
            'archived_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'archive_reason' => $this->faker->sentence(),
        ]);
    }

    public function withPreRegistration(): static
    {
        return $this->state(fn(array $attributes) => [
            'allow_pre_registration' => true,
            'pre_registration_deadline' => $this->faker->dateTimeBetween('now', $attributes['date']),
        ]);
    }
}