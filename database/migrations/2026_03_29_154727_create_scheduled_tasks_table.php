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
        Schema::create('scheduled_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('production_process_id')->constrained();
            $table->enum('task_type', ['production', 'assembly', 'quality_check', 'packaging', 'delivery_prep'])->default('production');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->dateTime('scheduled_start_at');
            $table->dateTime('scheduled_end_at');
            $table->dateTime('actual_start_at')->nullable();
            $table->dateTime('actual_end_at')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'paused', 'cancelled'])->default('scheduled');
            $table->integer('estimated_duration_hours')->default(0);
            $table->json('assigned_resources')->nullable(); // Equipment, personnel
            $table->integer('progress_percentage')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('production_process_id');
            $table->index('status');
            $table->index('scheduled_start_at');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_tasks');
    }
};
