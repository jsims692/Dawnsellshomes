<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

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

    'lead_webhook' => [
        'url' => env('LEAD_WEBHOOK_URL'),
    ],

    'kvcore' => [
        'token' => env('KVCORE_API_TOKEN'),
    ],

    'lead_notify' => [
        // comma-separated override via env; defaults to Josh + Dawn
        'recipients' => array_filter(array_map('trim', explode(',', env('LEAD_NOTIFY_EMAILS', 'jsims692@gmail.com,simsre2000@yahoo.com')))),
    ],

];
