<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'admin@example.com',
            'password' => Hash::make('adminpassword'),
            'role' => 'admin',
        ]);

        // Create a Staff user
        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'staff@example.com',
            'password' => Hash::make('staffpassword'),
            'role' => 'staff',
        ]);

}
}