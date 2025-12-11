<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Administration',
            'Application',
            'Bulletin',
            'Business',
            'Hardware',
            'Import Control',
            'Network Equipment',
            'SAN Storage',
            'SMK Interface',
            'Software Services',
            'User Management',
        ];

        foreach ($departments as $department) {
            Department::create(['name' => $department]);
        }
    }
}
