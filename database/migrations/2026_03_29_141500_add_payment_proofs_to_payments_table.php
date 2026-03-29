<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Menambah field untuk bukti pembayaran DP dan pelunasan yang terpisah
            if (!Schema::hasColumn('payments', 'payment_proof_dp')) {
                $table->string('payment_proof_dp')->nullable()->after('payment_proof')
                    ->comment('Path gambar bukti transfer DP');
            }
            if (!Schema::hasColumn('payments', 'payment_proof_full')) {
                $table->string('payment_proof_full')->nullable()->after('payment_proof_dp')
                    ->comment('Path gambar bukti transfer pelunasan (50% sisa)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_dp', 'payment_proof_full']);
        });
    }
};
