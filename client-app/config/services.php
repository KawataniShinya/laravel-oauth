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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'resource' => [
        'product_url' => env('RESOURCE_PRODUCT_URL'),
        'customer_url' => env('RESOURCE_CUSTOMER_URL'),
    ],

    'auth' => [
        'token_url' => env('AUTH_TOKEN_URL'),
        'password_grant_client_id' => env('AUTH_PASSWORD_GRANT_CLIENT_ID'),
        'password_grant_client_secret' => env('AUTH_PASSWORD_GRANT_CLIENT_SECRET'),
        'code_grant_url' => env('AUTH_CODE_GRANT_URL'),
        'code_grant_client_id' => env('AUTH_CODE_GRANT_CLIENT_ID'),
        'code_grant_client_secret' => env('AUTH_CODE_GRANT_CLIENT_SECRET'),
        'code_redirect_uri' => env('AUTH_CODE_REDIRECT_URI'),
        'oidc_code_grant_client_id' => env('OIDC_CODE_GRANT_CLIENT_ID'),
        'oidc_code_grant_client_secret' => env('OIDC_CODE_GRANT_CLIENT_SECRET'),
        'oidc_redirect_uri' => env('OIDC_REDIRECT_URI'),
    ],
];
