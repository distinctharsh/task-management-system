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
        Schema::table('complaints', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('assigned_by')->nullable()->after('assigned_to');

            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            //
            $table->dropForeign(['assigned_by']);
            $table->dropColumn('assigned_by');
        });
    }
};
