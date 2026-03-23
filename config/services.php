<?php

return [

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // --- AI API ---
    'ai' => [
        'url' => env('AI_API_URL'),
        'key' => env('AI_API_KEY'),
    ],

    // --- SEARCH API (Bing / Google / bármi) ---
    'search' => [
        'url' => env('SEARCH_API_URL'),
        'key' => env('SEARCH_API_KEY'),
    ],

];