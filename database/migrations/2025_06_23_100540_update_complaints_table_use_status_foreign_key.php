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
        // First, add the new status_id column
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('status')->constrained('statuses');
        });

        // Update existing complaints to use the new status_id
        // This will be done after we seed the statuses table
        // For now, we'll keep both columns

        // Later, we'll remove the old status enum column
        // Schema::table('complaints', function (Blueprint $table) {
        //     $table->dropColumn('status');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
    }
};
