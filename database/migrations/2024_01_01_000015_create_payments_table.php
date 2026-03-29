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
            $table->decimal('amount', 15, 2)->nullable()->comment('Total payment amount');
            $table->decimal('amount_paid', 15, 2)->default(0)->comment('Amount already paid');
            $table->decimal('expected_dp_amount', 15, 2)->nullable()->comment('Expected down payment amount');
            $table->string('payment_status', 30)->default('pending')->index()
                ->comment('pending, dp_paid, full_pending, paid, failed');
            $table->string('payment_method', 50)->nullable()->comment('transfer, cash, credit_card');
            $table->string('payment_channel', 40)->nullable()->comment('manual_dp, manual_full, midtrans');
            $table->string('transaction_id')->nullable()->index()->comment('ID unik transaksi');
            $table->string('payment_proof')->nullable()->comment('Path bukti transfer umum di storage');
            $table->string('payment_proof_dp')->nullable()->comment('Path bukti transfer DP di storage');
            $table->string('payment_proof_full')->nullable()->comment('Path bukti transfer pelunasan di storage');
            $table->timestamp('payment_date')->nullable()->comment('Waktu pembayaran berhasil diverifikasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};