<?php

return [
    'telegram' => [
        'store_bot_token' => env('TELEGRAM_STORE_BOT_TOKEN'),
        'deposit_bot_token' => env('TELEGRAM_DEPOSIT_BOT_TOKEN'),
        'admin_group_id' => env('TELEGRAM_ADMIN_GROUP_ID'),
        'allowed_callback_user_ids' => array_filter(array_map('intval', explode(',', (string) env('ALLOWED_USER_IDS', '')))),
        'deposit_webhook_secret' => env('TELEGRAM_DEPOSIT_WEBHOOK_SECRET'),
        'store_webhook_secret' => env('TELEGRAM_STORE_WEBHOOK_SECRET'),
    ],

    'mersal' => [
        'url' => env('MERSAL_API_URL'),
        'token' => env('MERSAL_API_TOKEN'),
    ],

    'supabase' => [
        'url' => env('SUPABASE_URL'),
        'anon_key' => env('SUPABASE_ANON_KEY'),
        'service_role_key' => env('SUPABASE_SERVICE_ROLE_KEY'),
        'storage_bucket' => env('SUPABASE_STORAGE_BUCKET', 'deposit-proofs'),
    ],
];
