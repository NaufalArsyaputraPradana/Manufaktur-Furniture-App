<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_processes', function (Blueprint $table) {
            $table->id();
            $table->string('production_code', 50)->unique()->comment('Format: PROD-YYYYMMDD-XXXXX');
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade')
                ->comment('Referensi ke pesanan utama');
            $table->foreignId('order_detail_id')
                ->nullable()
                ->constrained('order_details')
                ->onDelete('set null')
                ->comment('Referensi ke item produk spesifik dalam order');
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Staff produksi yang bertanggung jawab');
            $table->string('stage', 30)->nullable()->index()->comment('cutting, assembly, sanding, finishing, quality_control');
            $table->string('status', 30)->default('pending')->index()->comment('pending, in_progress, completed, paused, issue');
            $table->unsignedTinyInteger('progress_percentage')->default(0)->comment('Progress pengerjaan dalam rentang 0-100');
            $table->string('documentation')->nullable()->comment('Path file foto hasil produksi');
            $table->text('notes')->nullable()->comment('Catatan tambahan atau kendala produksi');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_processes');
    }
};