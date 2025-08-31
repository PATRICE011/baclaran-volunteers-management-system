<?php
namespace Database\Factories;

use App\Models\Task;
use App\Models\Ministry;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        // Set faker locale to English
        $this->faker = \Faker\Factory::create('en_US');
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['To Do', 'In Progress', 'Completed']),
            'ministry_id' => function () {
                // Get a random ministry that has a parent (actual ministry, not category)
                return Ministry::whereNotNull('parent_id')->inRandomOrder()->first()->id ?? 1;
            },
            'is_archived' => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'Completed',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'In Progress',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_archived' => true,
            'archived_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'archive_reason' => $this->faker->sentence(),
        ]);
    }
}
