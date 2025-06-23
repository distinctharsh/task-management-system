<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'pending',
                'slug' => 'pending',
                'color' => 'warning',
                'description' => 'Complaint is waiting to be assigned',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'assigned',
                'slug' => 'assigned',
                'color' => 'info',
                'description' => 'Complaint has been assigned to a team member',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'in_progress',
                'slug' => 'in-progress',
                'color' => 'primary',
                'description' => 'Work on the complaint is currently in progress',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'escalated',
                'slug' => 'escalated',
                'color' => 'danger',
                'description' => 'Complaint has been escalated to higher authority',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'resolved',
                'slug' => 'resolved',
                'color' => 'success',
                'description' => 'Complaint has been resolved successfully',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'closed',
                'slug' => 'closed',
                'color' => 'secondary',
                'description' => 'Complaint has been closed',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'reverted',
                'slug' => 'reverted',
                'color' => 'warning',
                'description' => 'Complaint has been reverted for further action',
                'is_active' => true,
                'sort_order' => 7,
            ],
            // Additional statuses that can be added dynamically
            [
                'name' => 'under_review',
                'slug' => 'under-review',
                'color' => 'info',
                'description' => 'Complaint is under technical review',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'waiting_for_client',
                'slug' => 'waiting-for-client',
                'color' => 'warning',
                'description' => 'Waiting for client response or information',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'on_hold',
                'slug' => 'on-hold',
                'color' => 'secondary',
                'description' => 'Complaint is temporarily on hold',
                'is_active' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
