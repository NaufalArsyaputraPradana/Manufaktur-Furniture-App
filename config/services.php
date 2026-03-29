<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ExchangeRate API (Currency Conversion)
    | Daftar di: https://www.exchangerate-api.com
    |--------------------------------------------------------------------------
    */
    'exchangerate' => [
        'api_key' => env('EXCHANGERATE_API_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Translate API
    | Aktifkan Cloud Translation API di Google Cloud Console
    |--------------------------------------------------------------------------
    */
    'google' => [
        'translate_api_key' => env('GOOGLE_TRANSLATE_API_KEY', ''),
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI', rtrim((string) env('APP_URL', 'http://localhost'), '/') . '/auth/google/callback'),
    ],

    'rajaongkir' => [
        'key' => env('RAJAONGKIR_API_KEY'),
    ],

];
