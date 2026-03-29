<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankAccount;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'bank_name' => 'BCA (Bank Central Asia)',
                'account_holder' => 'PT. Manufaktur Furniture',
                'account_number' => '1234567890',
                'is_active' => true,
                'notes' => 'Rekening utama untuk pembayaran DP dan pelunasan',
            ],
            [
                'bank_name' => 'Mandiri (Bank Mandiri)',
                'account_holder' => 'PT. Manufaktur Furniture',
                'account_number' => '9876543210',
                'is_active' => true,
                'notes' => 'Rekening alternatif',
            ],
            [
                'bank_name' => 'BNI (Bank Negara Indonesia)',
                'account_holder' => 'PT. Manufaktur Furniture',
                'account_number' => '5555555555',
                'is_active' => false,
                'notes' => 'Tidak aktif (sedang pembaruan)',
            ],
        ];

        foreach ($banks as $bank) {
            BankAccount::create($bank);
        }
    }
}

