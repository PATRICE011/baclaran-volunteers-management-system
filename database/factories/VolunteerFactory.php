<?php

namespace Database\Factories;

use App\Models\Volunteer;
use App\Models\VolunteerDetail;
use App\Models\OtherAffiliation;
use App\Models\VolunteerTimeline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Volunteer>
 */
class VolunteerFactory extends Factory
{
    protected $model = Volunteer::class;

    public function definition(): array
    {
        return [
            'nickname' => $this->faker->userName,
            'date_of_birth' => $this->faker->date(),
            'sex' => Arr::random(['Male', 'Female']),
            'address' => $this->faker->address,
            'mobile_number' => $this->faker->phoneNumber,
            'email_address' => $this->faker->unique()->safeEmail,
            'occupation' => $this->faker->jobTitle,
            'civil_status' => Arr::random(['Single', 'Married', 'Widow/er', 'Separated', 'Church', 'Civil', 'Others']),
            'sacraments_received' => $this->faker->randomElements(['Baptism', 'First Communion', 'Confirmation'], rand(1, 3)),
            'formations_received' => $this->faker->randomElements(['BOS', 'BFF', 'YES'], rand(1, 2)),
            'profile_picture' => $this->faker->imageUrl(400, 400), // Generate a random image URL
            'is_archived' => false,
            'archived_at' => null,
            'archived_by' => null,
            'archive_reason' => null,
        ];
    }

    /**
     * Indicate that the volunteer is archived.
     *
     * @return static
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_archived' => true,
            'archived_at' => Carbon::now(), // Use Carbon to set current date and time
            'archived_by' => \App\Models\User::factory(), // Create an archiver user
            'archive_reason' => $this->faker->sentence(), // Random archive reason
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Volunteer $volunteer) {
            // Create VolunteerDetail for the volunteer
            VolunteerDetail::factory()->create([
                'volunteer_id' => $volunteer->id,
            ]);

            // Create 2 other affiliations for the volunteer
            OtherAffiliation::factory()->count(2)->create([
                'volunteer_id' => $volunteer->id,
            ]);

            // Create a random number of VolunteerTimeline entries for the volunteer
            VolunteerTimeline::factory()->count(rand(1, 3))->create([
                'volunteer_id' => $volunteer->id,
            ]);
        });
    }
}
