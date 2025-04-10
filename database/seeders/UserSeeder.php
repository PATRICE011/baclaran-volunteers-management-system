<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create an Admin user
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpassword'), // Use a hashed password
            'role' => 'admin',  // Set role to admin
        ]);

        // Create a Staff user
        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'staff@example.com',
            'password' => Hash::make('staffpassword'), // Use a hashed password
            'role' => 'staff', // Set role to staff
        ]);

        // Optionally, create multiple random users (staff) using Faker
        // \App\Models\User::factory(10)->create();  
    }
}

