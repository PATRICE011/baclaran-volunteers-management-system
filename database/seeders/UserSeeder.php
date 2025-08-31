<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create 3 Admin users
        User::create([
            'email' => 'baclaranchurchmultimedia@gmail.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // User::create([
        //     'email' => 'parish.admin@baclaran.church',
        //     'password' => Hash::make('adminpassword'),
        //     'role' => 'admin',
        //     'email_verified_at' => now(),
        // ]);

        // User::create([
        //     'email' => 'system.admin@baclaran.church',
        //     'password' => Hash::make('adminpassword'),
        //     'role' => 'admin',
        //     'email_verified_at' => now(),
        // ]);


        // User::create([
        //     'email' => 'staff@example.com',
        //     'password' => Hash::make('staffpassword'),
        //     'role' => 'staff',
        //     'email_verified_at' => now(),
        // ]);


        // $staffEmails = [
        //     'michael.rodriguez@baclaran.church',
        //     'sarah.johnson@baclaran.church',
        //     'david.chen@baclaran.church',
        //     'maria.garcia@baclaran.church',
        //     'james.wilson@baclaran.church',
        //     'anna.martinez@baclaran.church',
        //     'robert.taylor@baclaran.church',
        //     'lisa.anderson@baclaran.church',
        //     'christopher.brown@baclaran.church',
        //     'jennifer.davis@baclaran.church',
        // ];

        // foreach ($staffEmails as $email) {
        //     User::create([
        //         'email' => $email,
        //         'password' => Hash::make('password123'), 
        //         'role' => 'staff',
        //         'email_verified_at' => now(),
        //     ]);
        // }
    }
}