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

    /*
    | Shiprocket Engage uses the same Shiprocket account + API user as shipping.
    | Values set here (via .env) take priority over the Admin → Settings values.
    */
    'shiprocket_engage' => [
        'email' => env('SHIPROCKET_ENGAGE_EMAIL'),
        'password' => env('SHIPROCKET_ENGAGE_PASSWORD'),
        'base_url' => env('SHIPROCKET_ENGAGE_BASE_URL', 'https://apiv2.shiprocket.in/v1/external'),
        'timeout' => (int) env('SHIPROCKET_ENGAGE_TIMEOUT', 20),
        // Only honoured when APP_ENV=local — never disables SSL checks on live.
        'verify_ssl' => env('SHIPROCKET_ENGAGE_VERIFY_SSL', true),
    ],

    /*
    | Shiprocket Checkout (Fastrr one-click checkout) — separate from Shiprocket Shipping.
    | Admin → Settings → Shiprocket Checkout manages these; .env values (if set) take priority.
    | Hosts/scripts are from the official Fastrr API documentation.
    */
    'shiprocket_checkout' => [
        'api_key' => env('SHIPROCKET_CHECKOUT_API_KEY'),
        'secret_key' => env('SHIPROCKET_CHECKOUT_SECRET_KEY'),
        'environment' => env('SHIPROCKET_CHECKOUT_ENV'),
        'base_url' => env('SHIPROCKET_CHECKOUT_BASE_URL'),
        'timeout' => (int) env('SHIPROCKET_CHECKOUT_TIMEOUT', 15),
        'environments' => [
            'production' => [
                'base_url' => 'https://checkout-api.shiprocket.com',
                'script' => 'https://checkout-ui.shiprocket.com/assets/js/channels/shopify.js',
                'style' => 'https://checkout-ui.shiprocket.com/assets/styles/shopify.css',
            ],
            'staging' => [
                'base_url' => 'https://fastrr-api-dev.pickrr.com',
                'script' => 'https://customcheckoutfastrr.netlify.app/assets/js/channels/shopify.js',
                'style' => 'https://customcheckoutfastrr.netlify.app/assets/styles/shopify.css',
            ],
        ],
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
