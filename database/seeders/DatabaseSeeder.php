<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(RoleSeeder::class);
        // $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(MinistrySeeder::class);
        // $this->call(ProductSeeder::class);
        // $this->call(CategorySeeder::class);
        // $this->call(TagSeeder::class);
        // $this->call(OrderSeeder::class);
        // $this->call(CommentSeeder::class);
        // Add other seeders as needed, but commented out for deployment
    }
}
