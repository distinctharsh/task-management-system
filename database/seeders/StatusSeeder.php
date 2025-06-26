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
                'name' => 'unassigned',
                'slug' => 'unassigned',
                'color' => 'warning',
                'description' => 'Ticket is created and not assigned to anyone yet',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'assigned',
                'slug' => 'assigned',
                'color' => 'info',
                'description' => 'Ticket has been assigned to a user by manager or VM',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'pending_with_vendor',
                'slug' => 'pending-with-vendor',
                'color' => 'primary',
                'description' => 'Assigned user has set status to pending with vendor',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'pending_with_user',
                'slug' => 'pending-with-user',
                'color' => 'primary',
                'description' => 'Assigned user has set status to pending with user',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'assign_to_me',
                'slug' => 'assign-to-me',
                'color' => 'info',
                'description' => 'Ticket reverted to previous user who assigned it to current user',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'completed',
                'slug' => 'completed',
                'color' => 'success',
                'description' => 'Assigned user has completed the work and marked as completed',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'closed',
                'slug' => 'closed',
                'color' => 'secondary',
                'description' => 'Manager has verified and closed the ticket',
                'is_active' => true,
                'sort_order' => 7,
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
