<?php

namespace Database\Factories;

use App\Models\Ministry;
use App\Models\Volunteer;
use App\Models\VolunteerDetail;
use App\Models\OtherAffiliation;
use App\Models\VolunteerTimeline;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

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
        ];
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
