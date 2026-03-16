<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')
                ->unique()
                ->index()
                ->comment('Kunci identitas pengaturan (misal: site_name)');
            $table->text('value')
                ->nullable()
                ->comment('Nilai dari pengaturan (bisa berupa teks atau JSON)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};