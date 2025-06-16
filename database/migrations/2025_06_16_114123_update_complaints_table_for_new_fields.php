<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Remove old fields
            $table->dropColumn(['subject', 'location']);
            
            // Add new fields
            $table->string('network_type');
            $table->string('vertical');
            $table->string('user_name');
            $table->string('file_path')->nullable();
            $table->string('section');
            $table->string('intercom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('complaints', function (Blueprint $table) {
            // Reverse the changes
            $table->string('subject');
            $table->string('location');
            
            $table->dropColumn([
                'network_type',
                'vertical',
                'user_name',
                'file_path',
                'section',
                'intercom'
            ]);
        });
    }
};
