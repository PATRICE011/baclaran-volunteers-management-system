<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MinistrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('ministries')->truncate();
        Schema::enableForeignKeyConstraints();

        // Insert main ministry categories
        $mainCategories = [
            [
                'ministry_name' => 'Liturgical Ministries',
                'parent_id' => null,
                'ministry_type' => 'PARISH MINISTRIES',
            ],
            [
                'ministry_name' => 'Pastoral Ministries',
                'parent_id' => null,
                'ministry_type' => 'PASTORAL',
            ],
            [
                'ministry_name' => 'Social Mission Apostolate',
                'parent_id' => null,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
        ];

        DB::table('ministries')->insert($mainCategories);

        // Get dynamically assigned IDs
        $liturgicalId = DB::table('ministries')->where('ministry_name', 'Liturgical Ministries')->value('id');
        $pastoralId = DB::table('ministries')->where('ministry_name', 'Pastoral Ministries')->value('id');
        $socialId = DB::table('ministries')->where('ministry_name', 'Social Mission Apostolate')->value('id');

        // Insert Liturgical Ministries
        $liturgicalMinistries = [
            [
                'ministry_name' => 'Ministry of Lectors, Commentators, and Psalmists',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
            [
                'ministry_name' => 'Mother Butler Guild',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
            [
                'ministry_name' => 'Our Mother of Perpetual Help Altar Servers',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
            [
                'ministry_name' => 'Extraordinary Ministers of Holy Communion',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
            [
                'ministry_name' => 'Social Communication and Media Ministry',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
            [
                'ministry_name' => 'Ministry of Ushers and Greeters',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
            [
                'ministry_name' => 'Ministry of Collectors',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
            [
                'ministry_name' => 'Redemptorist Music Ministry',
                'parent_id' => $liturgicalId,
                'ministry_type' => 'LITURGICAL',
            ],
        ];

        DB::table('ministries')->insert($liturgicalMinistries);

        // Get Redemptorist Music Ministry ID
        $rmmId = DB::table('ministries')->where('ministry_name', 'Redemptorist Music Ministry')->value('id');

        // Insert Music Ministry Sub-groups (Choirs)
        $musicSubgroups = [
            [
                'ministry_name' => 'St. Therese Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'St. John Neumann Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'St. Gerard Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'St. Clement Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Our Mother of Perpetual Help Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'St. Cecilia Rondalla Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Sarnelli Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'St. Alphonsus Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Alphonsian Vocal Ensemble',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Tinig Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'San Lorenzo Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'St. Gregory Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Blessed Kaspar Stanggasinger Church Musicians',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Redemptorist Children\'s Choir',
                'parent_id' => $rmmId,
                'ministry_type' => 'SUB_GROUP',
            ],
        ];

        DB::table('ministries')->insert($musicSubgroups);

        // Insert Pastoral Ministries
        $pastoralMinistries = [
            [
                'ministry_name' => 'Special Ministry for the Deaf',
                'parent_id' => $pastoralId,
                'ministry_type' => 'PASTORAL',
            ],
            [
                'ministry_name' => 'Redemptorist Children\'s Committee',
                'parent_id' => $pastoralId,
                'ministry_type' => 'PASTORAL',
            ],
            [
                'ministry_name' => 'Redemptorist Youth Mission',
                'parent_id' => $pastoralId,
                'ministry_type' => 'PASTORAL',
            ],
            [
                'ministry_name' => 'Solidarity Assistance Committee',
                'parent_id' => $pastoralId,
                'ministry_type' => 'PASTORAL',
            ],
            [
                'ministry_name' => 'Confraternity of Our Mother of Perpetual Help-Baclaran',
                'parent_id' => $pastoralId,
                'ministry_type' => 'PASTORAL',
            ],
            [
                'ministry_name' => 'Pilgrimage Team',
                'parent_id' => $pastoralId,
                'ministry_type' => 'PASTORAL',
            ],
        ];

        DB::table('ministries')->insert($pastoralMinistries);

        // Get Children's Committee and Youth Mission IDs
        $childrenCommitteeId = DB::table('ministries')->where('ministry_name', 'Redemptorist Children\'s Committee')->value('id');
        $youthMissionId = DB::table('ministries')->where('ministry_name', 'Redemptorist Youth Mission')->value('id');

        // Insert Children's Committee Sub-groups
        $childrenSubgroups = [
            [
                'ministry_name' => 'Redemptorist Commission on Catechesis',
                'parent_id' => $childrenCommitteeId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Redemptorist Children\'s Choir',
                'parent_id' => $childrenCommitteeId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Redemptorist Children\'s Animators',
                'parent_id' => $childrenCommitteeId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Redemptorist Children Lectors and Commentators',
                'parent_id' => $childrenCommitteeId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Our Mother of Perpetual Help Altar Servers',
                'parent_id' => $childrenCommitteeId,
                'ministry_type' => 'SUB_GROUP',
            ],
        ];

        DB::table('ministries')->insert($childrenSubgroups);

        // Insert Youth Mission Sub-groups
        $youthSubgroups = [
            [
                'ministry_name' => 'Alphonsian Vocal Ensemble',
                'parent_id' => $youthMissionId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Alphonsian Repertory Theater',
                'parent_id' => $youthMissionId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Alphonsian Youth Band',
                'parent_id' => $youthMissionId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Alphonsian Dance Praise',
                'parent_id' => $youthMissionId,
                'ministry_type' => 'SUB_GROUP',
            ],
            [
                'ministry_name' => 'Alphonsian Young Adults',
                'parent_id' => $youthMissionId,
                'ministry_type' => 'SUB_GROUP',
            ],
        ];

        DB::table('ministries')->insert($youthSubgroups);

        // Insert Social Mission Ministries
        $socialMissionMinistries = [
            [
                'ministry_name' => 'Saint Gerard Family Life Ministry',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
            [
                'ministry_name' => 'Redemptorist Skills Training and Livelihood Program',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
            [
                'ministry_name' => 'St. John Neumann Migrants Center',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
            [
                'ministry_name' => 'Redemptorist Medical and Dental Services',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
            [
                'ministry_name' => 'Justice Peace and Integrity of Creation',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
            [
                'ministry_name' => 'Crisis Intervention Center',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
            [
                'ministry_name' => 'Redemptorist Alternative Learning System',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
            [
                'ministry_name' => 'Perpetual Help Women\'s Program Center',
                'parent_id' => $socialId,
                'ministry_type' => 'SOCIAL_MISSION',
            ],
        ];

        DB::table('ministries')->insert($socialMissionMinistries);
    }
}
