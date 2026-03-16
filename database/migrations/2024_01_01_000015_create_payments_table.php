<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->decimal('amount', 15, 2)->nullable()->comment('Nominal pembayaran');
            $table->enum('payment_status', ['unpaid', 'paid', 'failed'])
                ->default('unpaid')
                ->index();
            $table->string('payment_method', 50)->nullable()->comment('transfer, cash, credit_card');
            $table->string('transaction_id')->nullable()->index()->comment('ID unik transaksi');
            $table->string('payment_proof')->nullable()->comment('Path gambar bukti transfer di storage');
            $table->timestamp('payment_date')->nullable()->comment('Waktu pembayaran berhasil diverifikasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};