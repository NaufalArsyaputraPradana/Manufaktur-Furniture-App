<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('google_id');
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'shipping_status')) {
                $table->string('shipping_status', 40)->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'courier')) {
                $table->string('courier', 100)->nullable()->after('shipping_status');
            }
            if (!Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number', 120)->nullable()->after('courier');
            }
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            }
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'amount_paid')) {
                $table->decimal('amount_paid', 15, 2)->default(0)->after('amount');
            }
            if (!Schema::hasColumn('payments', 'expected_dp_amount')) {
                $table->decimal('expected_dp_amount', 15, 2)->nullable()->after('amount_paid');
            }
            if (!Schema::hasColumn('payments', 'payment_channel')) {
                $table->string('payment_channel', 40)->nullable()->after('payment_method');
            }
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("UPDATE payments SET payment_status = 'pending' WHERE payment_status = 'unpaid'");
            DB::statement(
                "ALTER TABLE payments MODIFY payment_status VARCHAR(30) NOT NULL DEFAULT 'pending'"
            );
        } else {
            DB::table('payments')
                ->where('payment_status', 'unpaid')
                ->update(['payment_status' => 'pending']);
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("UPDATE payments SET payment_status = 'unpaid' WHERE payment_status = 'pending'");
            DB::statement(
                "ALTER TABLE payments MODIFY payment_status ENUM('unpaid','paid','failed') NOT NULL DEFAULT 'unpaid'"
            );
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'expected_dp_amount', 'payment_channel']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_status',
                'courier',
                'tracking_number',
                'shipped_at',
                'delivered_at',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
