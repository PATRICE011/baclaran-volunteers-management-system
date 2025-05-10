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

        // Insert main ministry categories first
        $mainCategories = [
            [
                'id' => 1,
                'ministry_name' => 'Liturgical Ministries',
                'ministry_code' => 'LITURGICAL',
                'parent_id' => null,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Ministries focused on liturgy and worship services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'ministry_name' => 'Pastoral Ministries',
                'ministry_code' => 'PASTORAL',
                'parent_id' => null,
                'ministry_type' => 'PASTORAL',
                'description' => 'Ministries focused on pastoral care and community building',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'ministry_name' => 'Social Mission Apostolate',
                'ministry_code' => 'SOCIAL',
                'parent_id' => null,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Ministries focused on social justice and community outreach',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('ministries')->insert($mainCategories);

        // Insert Liturgical Ministries
        $liturgicalMinistries = [
            [
                'id' => 101,
                'ministry_name' => 'Ministry of Lectors, Commentators, and Psalmists',
                'ministry_code' => 'MLCP',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Proclaims the Word of God during liturgical celebrations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 102,
                'ministry_name' => 'Mother Butler Guild',
                'ministry_code' => 'MBG',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Maintains the church altar and liturgical vestments',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 103,
                'ministry_name' => 'Our Mother of Perpetual Help Altar Servers',
                'ministry_code' => 'OMPHAS',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Assists priests during Mass and other liturgical functions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 104,
                'ministry_name' => 'Extraordinary Ministers of Holy Communion',
                'ministry_code' => 'EMHC',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Assists in distributing Holy Communion during Mass',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 105,
                'ministry_name' => 'Social Communication and Media Ministry',
                'ministry_code' => 'SocComM',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Manages church communications and media production',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 106,
                'ministry_name' => 'Ministry of Ushers and Greeters',
                'ministry_code' => 'MUG',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Welcomes people to church services and assists with seating',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 107,
                'ministry_name' => 'Ministry of Collectors',
                'ministry_code' => 'MoC',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Collects offerings during church services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 108,
                'ministry_name' => 'Redemptorist Music Ministry',
                'ministry_code' => 'RMM',
                'parent_id' => 1,
                'ministry_type' => 'LITURGICAL',
                'description' => 'Provides music for liturgical celebrations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('ministries')->insert($liturgicalMinistries);

        // Insert Music Ministry Sub-groups (Choirs)
        $musicSubgroups = [
            [
                'ministry_name' => 'St. Therese Choir',
                'ministry_code' => 'STC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'St. John Neumann Choir',
                'ministry_code' => 'SJNC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'St. Gerard Choir',
                'ministry_code' => 'SGC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'St. Clement Choir',
                'ministry_code' => 'SCC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Our Mother of Perpetual Help Choir',
                'ministry_code' => 'OMPHC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'St. Cecilia Rondalla Choir',
                'ministry_code' => 'SCRC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Specialized choir with string instruments',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Sarnelli Choir',
                'ministry_code' => 'SC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'St. Alphonsus Choir',
                'ministry_code' => 'SAC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Alphonsian Vocal Ensemble',
                'ministry_code' => 'AVE',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Select vocal ensemble for special occasions',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Tinig Choir',
                'ministry_code' => 'TC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'San Lorenzo Choir',
                'ministry_code' => 'SLC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'St. Gregory Choir',
                'ministry_code' => 'SGrC',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Church choir serving specific Mass schedules',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Blessed Kaspar Stanggasinger Church Musicians',
                'ministry_code' => 'BKSCM',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Instrumental musicians for church services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Redemptorist Children\'s Choir',
                'ministry_code' => 'RCCh',
                'parent_id' => 108,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Children\'s choir for special liturgical celebrations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('ministries')->insert($musicSubgroups);

        // Insert Pastoral Ministries
        $pastoralMinistries = [
            [
                'id' => 201,
                'ministry_name' => 'Special Ministry for the Deaf',
                'ministry_code' => 'SMD',
                'parent_id' => 2,
                'ministry_type' => 'PASTORAL',
                'description' => 'Ministry providing services for the deaf community',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 202,
                'ministry_name' => 'Redemptorist Children\'s Committee',
                'ministry_code' => 'RCCOM',
                'parent_id' => 2,
                'ministry_type' => 'PASTORAL',
                'description' => 'Committee overseeing all children\'s ministries',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 203,
                'ministry_name' => 'Redemptorist Youth Mission',
                'ministry_code' => 'RYM',
                'parent_id' => 2,
                'ministry_type' => 'PASTORAL',
                'description' => 'Ministry focused on youth formation and activities',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 204,
                'ministry_name' => 'Solidarity Assistance Committee',
                'ministry_code' => 'SAC',
                'parent_id' => 2,
                'ministry_type' => 'PASTORAL',
                'description' => 'Provides assistance to those in need within the community',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 205,
                'ministry_name' => 'Confraternity of Our Mother of Perpetual Help-Baclaran',
                'ministry_code' => 'COMPH',
                'parent_id' => 2,
                'ministry_type' => 'PASTORAL',
                'description' => 'Promotes devotion to Our Mother of Perpetual Help',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 206,
                'ministry_name' => 'Pilgrimage Team',
                'ministry_code' => 'PT',
                'parent_id' => 2,
                'ministry_type' => 'PASTORAL',
                'description' => 'Organizes and facilitates pilgrimages',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('ministries')->insert($pastoralMinistries);

        // Insert Children's Committee Sub-groups
        $childrenSubgroups = [
            [
                'ministry_name' => 'Redemptorist Commission on Catechesis',
                'ministry_code' => 'RCC',
                'parent_id' => 202,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Provides catechism education for children',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Redemptorist Children\'s Choir',
                'ministry_code' => 'RCCh-RCCOM',
                'parent_id' => 202,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Children\'s choir for liturgical services',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Redemptorist Children\'s Animators',
                'ministry_code' => 'RCA',
                'parent_id' => 202,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Animates children\'s activities and events',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Redemptorist Children Lectors and Commentators',
                'ministry_code' => 'RCLC',
                'parent_id' => 202,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Children who serve as lectors during liturgical celebrations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Our Mother of Perpetual Help Altar Servers',
                'ministry_code' => 'OMPHAS-RCCOM',
                'parent_id' => 202,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Children altar servers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('ministries')->insert($childrenSubgroups);

        // Insert Youth Mission Sub-groups
        $youthSubgroups = [
            [
                'ministry_name' => 'Alphonsian Vocal Ensemble',
                'ministry_code' => 'AVE-RYM',
                'parent_id' => 203,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Youth vocal ensemble',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Alphonsian Repertory Theater',
                'ministry_code' => 'ART',
                'parent_id' => 203,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Youth theater group',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Alphonsian Youth Band',
                'ministry_code' => 'AYB',
                'parent_id' => 203,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Youth musical band',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Alphonsian Dance Praise',
                'ministry_code' => 'ADP',
                'parent_id' => 203,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Youth liturgical dance group',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'ministry_name' => 'Alphonsian Young Adults',
                'ministry_code' => 'AYA',
                'parent_id' => 203,
                'ministry_type' => 'SUB_GROUP',
                'description' => 'Young adult ministry for those beyond college age',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('ministries')->insert($youthSubgroups);

        // Insert Social Mission Ministries
        $socialMissionMinistries = [
            [
                'id' => 301,
                'ministry_name' => 'Saint Gerard Family Life Ministry',
                'ministry_code' => 'SGFLM',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Ministry focused on family life and family issues',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 302,
                'ministry_name' => 'Redemptorist Skills Training and Livelihood Program',
                'ministry_code' => 'RSTLP',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Provides skills training and livelihood opportunities',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 303,
                'ministry_name' => 'St. John Neumann Migrants Center',
                'ministry_code' => 'SJNMC',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Serves the needs of migrant workers and their families',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 304,
                'ministry_name' => 'Redemptorist Medical and Dental Services',
                'ministry_code' => 'RMDS',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Provides basic medical and dental care to the community',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 305,
                'ministry_name' => 'Justice Peace and Integrity of Creation',
                'ministry_code' => 'JPIC',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Promotes social justice and environmental stewardship',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 306,
                'ministry_name' => 'Crisis Intervention Center',
                'ministry_code' => 'CIC',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Provides immediate assistance during crisis situations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 307,
                'ministry_name' => 'Redemptorist Alternative Learning System',
                'ministry_code' => 'RALS',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Provides alternative education options for out-of-school youth',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 308,
                'ministry_name' => 'Perpetual Help Women\'s Program Center',
                'ministry_code' => 'PHWPC',
                'parent_id' => 3,
                'ministry_type' => 'SOCIAL_MISSION',
                'description' => 'Programs focused on women\'s development and empowerment',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('ministries')->insert($socialMissionMinistries);
    }
}