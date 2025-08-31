<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Volunteer;
use App\Models\Event;
use App\Models\Task;
use App\Models\Ministry;
use App\Models\VolunteerDetail;
use App\Models\VolunteerSacrament;
use App\Models\VolunteerFormation;
use App\Models\VolunteerTimeline;
use App\Models\OtherAffiliation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(MinistrySeeder::class);

        // Get existing ministries (excluding main categories)
        $ministries = Ministry::whereNotNull('parent_id')->get();

        // Get existing users for pre_registered_by references
        $users = User::all();

        // If no users exist, create some
        if ($users->isEmpty()) {
            $users = User::factory(5)->create();
        }

        // Create 500 volunteers with complete profiles
        $volunteers = Volunteer::factory(500)->create();

        foreach ($volunteers as $volunteer) {
            // Create volunteer detail for each volunteer
            VolunteerDetail::factory()->create([
                'volunteer_id' => $volunteer->id,
                'ministry_id' => $ministries->random()->id,
            ]);

            // Create 1-3 sacraments per volunteer
            VolunteerSacrament::factory()
                ->count($this->faker->numberBetween(1, 3))
                ->create(['volunteer_id' => $volunteer->id]);

            // Create 0-2 formations per volunteer
            VolunteerFormation::factory()
                ->count($this->faker->numberBetween(0, 2))
                ->create(['volunteer_id' => $volunteer->id]);

            // Create 1-2 timeline entries per volunteer
            VolunteerTimeline::factory()
                ->count($this->faker->numberBetween(1, 2))
                ->create(['volunteer_id' => $volunteer->id]);

            // Create 0-2 other affiliations per volunteer
            OtherAffiliation::factory()
                ->count($this->faker->numberBetween(0, 2))
                ->create(['volunteer_id' => $volunteer->id]);
        }

        // Create 50 events
        $events = Event::factory(50)->create([
            'ministry_id' => fn() => $ministries->random()->id,
        ]);

        // Assign random volunteers to events
        foreach ($events as $event) {
            $eventVolunteers = $volunteers->random($this->faker->numberBetween(5, 20));

            foreach ($eventVolunteers as $volunteer) {
                $event->volunteers()->attach($volunteer->id, [
                    'attendance_status' => $this->faker->randomElement(['present', 'absent', 'pending']),
                    'checked_in_at' => $this->faker->optional(0.6)->dateTimeBetween($event->date, $event->date . ' +1 day'),
                    'pre_registered_at' => $this->faker->optional(0.4)->dateTimeBetween('-30 days', $event->date),
                    'pre_registered_by' => $this->faker->optional(0.4)->randomElement($users->pluck('id')->toArray()),
                ]);
            }
        }

        // Create 50 tasks
        Task::factory(50)->create([
            'ministry_id' => fn() => $ministries->random()->id,
        ]);

        // Create some archived records (10% of each)
        $archivedVolunteers = Volunteer::factory(50)->archived()->create();

        // Create details for archived volunteers too
        foreach ($archivedVolunteers as $volunteer) {
            VolunteerDetail::factory()->create([
                'volunteer_id' => $volunteer->id,
                'ministry_id' => $ministries->random()->id,
            ]);
        }

        Event::factory(5)->archived()->create([
            'ministry_id' => fn() => $ministries->random()->id,
        ]);
        Task::factory(5)->archived()->create([
            'ministry_id' => fn() => $ministries->random()->id,
        ]);
    }

    private $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create('en_US');
    }
}