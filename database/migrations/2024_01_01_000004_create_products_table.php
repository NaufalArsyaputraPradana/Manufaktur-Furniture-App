<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('sku', 50)->unique()->comment('Stock Keeping Unit');
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 15, 2)->nullable();
            $table->string('dimensions', 100)->nullable()->comment('PxLxT');
            $table->string('wood_type', 100)->nullable()->comment('Jenis kayu');
            $table->string('finishing_type', 100)->nullable()->comment('Jenis finishing');
            $table->integer('estimated_production_days')->default(7);
            $table->json('images')->nullable()->comment('Array path gambar');
            $table->boolean('is_customizable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};