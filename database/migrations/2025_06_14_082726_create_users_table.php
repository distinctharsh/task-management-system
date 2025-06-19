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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('full_name', 100);
            $table->enum('role', ['admin', 'manager', 'vm', 'nfo', 'client'])->default('client');
            $table->unsignedBigInteger('vertical_id')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            
            $table->foreign('vertical_id')->references('id')->on('verticals')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
