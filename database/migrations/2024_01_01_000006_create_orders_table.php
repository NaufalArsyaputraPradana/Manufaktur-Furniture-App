<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique()->comment('Format: ORD-YYYYMMDD-XXXX');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_production',
                'completed',
                'cancelled',
                'on_hold'
            ])->default('pending');
            $table->string('shipping_status', 40)->nullable()->comment('processing, shipped, delivered');
            $table->string('courier', 100)->nullable()->comment('Shipping courier name');
            $table->string('tracking_number', 120)->nullable()->comment('Tracking number from courier');
            $table->text('shipping_address')->nullable();
            $table->string('phone', 20)->nullable()->comment('Shipping contact phone');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->date('order_date')->useCurrent();
            $table->date('expected_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->timestamp('shipped_at')->nullable()->comment('When order was shipped');
            $table->timestamp('delivered_at')->nullable()->comment('When order was delivered');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};