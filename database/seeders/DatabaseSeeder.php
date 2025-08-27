<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Volunteer;
use App\Models\VolunteerDetail;
use App\Models\OtherAffiliation;
use App\Models\VolunteerTimeline;
use App\Models\VolunteerSacrament;
use App\Models\VolunteerFormation;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(MinistrySeeder::class);

        // Create volunteers with all related data
        Volunteer::factory()
            ->count(15)
            ->has(VolunteerDetail::factory()->count(1), 'detail')
            ->has(OtherAffiliation::factory()->count(2), 'affiliations')
            ->has(VolunteerTimeline::factory()->count(rand(1, 3)), 'timelines')
            ->has(VolunteerSacrament::factory()->count(rand(1, 3)), 'sacraments')
            ->has(VolunteerFormation::factory()->count(rand(1, 2)), 'formations')
            ->create();
    }
}