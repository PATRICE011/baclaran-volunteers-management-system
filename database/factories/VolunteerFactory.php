<?php

namespace Database\Factories;

use App\Models\Volunteer;
use App\Models\VolunteerDetail;
use App\Models\OtherAffiliation;
use App\Models\VolunteerTimeline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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
            'volunteer_id' => 'VOL-' . Str::upper(Str::random(8)), // Generate unique volunteer ID
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
        return $this->state(fn(array $attributes) => [
            'is_archived' => true,
            'archived_at' => Carbon::now(),
            'archived_by' => \App\Models\User::factory(),
            'archive_reason' => $this->faker->sentence(),
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Volunteer $volunteer) {
            VolunteerDetail::factory()->create([
                'volunteer_id' => $volunteer->id,
            ]);

            OtherAffiliation::factory()->count(2)->create([
                'volunteer_id' => $volunteer->id,
            ]);

            VolunteerTimeline::factory()->count(rand(1, 3))->create([
                'volunteer_id' => $volunteer->id,
            ]);
        });
    }
}