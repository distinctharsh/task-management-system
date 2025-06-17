<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('complaints', function (Blueprint $table) {
            // Remove old columns if they exist
            if (Schema::hasColumn('complaints', 'network_type')) {
                $table->dropColumn('network_type');
            }
            if (Schema::hasColumn('complaints', 'vertical')) {
                $table->dropColumn('vertical');
            }
            if (Schema::hasColumn('complaints', 'section')) {
                $table->dropColumn('section');
            }

            // Add foreign key columns if they don't exist
            if (!Schema::hasColumn('complaints', 'network_type_id')) {
                $table->foreignId('network_type_id')->nullable()->constrained('network_types');
            }
            if (!Schema::hasColumn('complaints', 'vertical_id')) {
                $table->foreignId('vertical_id')->nullable()->constrained('verticals');
            }
            if (!Schema::hasColumn('complaints', 'section_id')) {
                $table->foreignId('section_id')->nullable()->constrained('sections');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('complaints', function (Blueprint $table) {
            // Reverse the changes if needed
            $table->string('network_type')->nullable();
            $table->string('vertical')->nullable();
            $table->string('section')->nullable();

            $table->dropForeign(['network_type_id']);
            $table->dropForeign(['vertical_id']);
            $table->dropForeign(['section_id']);

            $table->dropColumn(['network_type_id', 'vertical_id', 'section_id']);
        });
    }
};
