<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 50)->index()->comment('sales, production, etc');
            $table->string('title');
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->json('data')->comment('Snapshot of report data at the time of generation');
            $table->foreignId('generated_by')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};