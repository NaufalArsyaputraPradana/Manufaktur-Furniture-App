<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_process_id')
                ->constrained('production_processes')
                ->onDelete('cascade')
                ->comment('Referensi ke proses produksi induk');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->comment('User/Staff yang melakukan input atau aksi');
            $table->string('stage', 30)->nullable()->index()->comment('cutting, assembly, sanding, finishing, quality_control');
            $table->string('action', 30)->index()->comment('started, in_progress, completed, paused, issue');
            $table->unsignedTinyInteger('progress_percentage')->default(0)->comment('Snapshot progress saat log ini dibuat (0-100)');
            $table->text('notes')->nullable()->comment('Keterangan aktivitas atau detail kendala/isu');
            $table->json('material_used')->nullable()->comment('Daftar material yang dikonsumsi dalam aksi ini');
            $table->json('documentation_images')->nullable()->comment('Array berisi path foto bukti pengerjaan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_logs');
    }
};