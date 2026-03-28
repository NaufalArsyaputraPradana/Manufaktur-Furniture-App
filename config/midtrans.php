<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    // Callback URL dengan path API (tanpa CSRF)
    'notification_url' => env('MIDTRANS_NOTIFICATION_URL'),
];
