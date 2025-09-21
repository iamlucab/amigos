<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | The default guard and password broker used by your application.
    |
    */

    'defaults' => [
        'guard' => 'web', // Default is web (admin/staff)
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | You may define multiple guards for different types of users.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'member' => [ // ✅ Custom guard for members
            'driver' => 'session',
            'provider' => 'members',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | These define how users are retrieved from your database.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'members' => [ // ✅ Eloquent provider for Member model
            'driver' => 'eloquent',
            'model' => App\Models\Member::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Configure password reset options for each user type.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],

        'members' => [ // ✅ Password reset config for members
            'provider' => 'members',
            'table' => 'password_resets', // You can use a different table if needed
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Number of seconds before password confirmation times out.
    |
    */

    'password_timeout' => 10800,

];
// End of config/auth.php