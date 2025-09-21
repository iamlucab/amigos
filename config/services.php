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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Social Login Services
    |--------------------------------------------------------------------------
    |
    | Configuration for social media authentication providers
    |
    */

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL', env('APP_URL') . '/auth/google/callback'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URL', env('APP_URL') . '/auth/facebook/callback'),
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URL', env('APP_URL') . '/auth/github/callback'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Service Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for SMS services used for OTP
    |
    */

    'semaphore' => [
        'api_key' => env('SEMAPHORE_API_KEY'),
        'sender_name' => env('SMS_SENDER_NAME', 'E-Bili'),
        'base_url' => env('SEMAPHORE_BASE_URL', 'https://semaphore.co/api/v4'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Cloud Messaging (FCM) push notifications
    |
    */

    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID', 'e-bili-online-2c581'),
        'credentials' => env('FIREBASE_CREDENTIALS_PATH', storage_path('app/firebase/firebase-service-account.json')),
        'database_url' => env('FIREBASE_DATABASE_URL'),
        'server_key' => env('FCM_SERVER_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | AliExpress OAuth Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for AliExpress OAuth integration
    |
    */

    
        'aliexpress' => [
            'client_id' => env('ALIEXPRESS_CLIENT_ID'),
            'client_secret' => env('ALIEXPRESS_CLIENT_SECRET'),
            'redirect' => env('ALIEXPRESS_REDIRECT_URL', env('APP_URL') . '/aliexpress/callback'),
        ],
];
