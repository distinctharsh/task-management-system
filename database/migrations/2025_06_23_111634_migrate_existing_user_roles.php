<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = Role::all()->pluck('id', 'name');

        $roleMapping = [
            'admin' => 'Admin',
            'manager' => 'Manager',
            'vm' => 'VM',
            'nfo' => 'NFO',
            'client' => 'Client',
        ];

        foreach ($roleMapping as $enumValue => $roleName) {
            if (isset($roles[$roleName])) {
                User::where('role', $enumValue)->update(['role_id' => $roles[$roleName]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::query()->update(['role_id' => null]);
    }
};
