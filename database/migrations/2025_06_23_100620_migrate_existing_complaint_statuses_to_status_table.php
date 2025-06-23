<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing complaints to use the new status_id
        $statusMapping = [
            'pending' => 1,
            'assigned' => 2,
            'in_progress' => 3,
            'escalated' => 4,
            'resolved' => 5,
            'closed' => 6,
            'reverted' => 7,
        ];

        foreach ($statusMapping as $oldStatus => $newStatusId) {
            DB::table('complaints')
                ->where('status', $oldStatus)
                ->update(['status_id' => $newStatusId]);
        }

        // Make status_id not nullable after migration
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status_id to nullable
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->change();
        });

        // Clear status_id values
        DB::table('complaints')->update(['status_id' => null]);
    }
};
