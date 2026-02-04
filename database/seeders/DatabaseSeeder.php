<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed departments, priorities, and statuses first
        $this->call([
            DepartmentSeeder::class,
            PrioritySeeder::class,
            StatusSeeder::class,
        ]);

        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'is_admin' => true,
        ]);

        // Create staff user
        User::factory()->create([
            'name' => 'Staff User',
            'username' => 'staff',
            'email' => 'staff@example.com',
            'is_admin' => false,
        ]);
    }
}
