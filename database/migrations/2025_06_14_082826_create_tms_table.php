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
        Schema::create('tms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('action_by')->constrained('users')->onDelete('cascade');
            $table->enum('action_type', ['created', 'assigned', 'reassigned', 'escalated', 'remark_added', 'resolved', 'closed']);
            $table->text('remarks')->nullable();
            $table->foreignId('previous_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('new_assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tms');
    }
};
