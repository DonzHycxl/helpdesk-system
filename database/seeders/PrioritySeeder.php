<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            'Critical',
            'Semi-critical',
            'Non-critical',
            'General Enquiries',
            'Change Request',
        ];

        foreach ($priorities as $priority) {
            Priority::create(['name' => $priority]);
        }
    }
}
