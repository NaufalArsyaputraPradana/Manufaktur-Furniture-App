<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Down payment (DP) — persentase dari total pesanan untuk pembayaran manual
    |--------------------------------------------------------------------------
    */
    'down_payment_percent' => (float) env('ORDER_DP_PERCENT', 50),

    /*
    |--------------------------------------------------------------------------
    | Rekening transfer manual (ditampilkan ke pelanggan)
    |--------------------------------------------------------------------------
    */
    'bank' => [
        'name'    => env('ORDER_BANK_NAME', 'BCA'),
        'account' => env('ORDER_BANK_ACCOUNT', '1234567890'),
        'holder'  => env('ORDER_BANK_HOLDER', 'UD Bisa Furniture'),
    ],

];
