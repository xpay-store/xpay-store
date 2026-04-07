<?php

return [
    'name' => env('APP_NAME', 'XPayStore'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Damascus'),
    'locale' => env('APP_LOCALE', 'ar'),
    'fallback_locale' => 'en',
    'faker_locale' => 'ar_SA',
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => array_filter(explode(',', (string) env('APP_PREVIOUS_KEYS', ''))),
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'admin_api_token' => env('ADMIN_API_TOKEN'),
    'admin_email' => env('ADMIN_EMAIL'),
    'frontend_url' => env('FRONTEND_URL', env('APP_URL')),
];
