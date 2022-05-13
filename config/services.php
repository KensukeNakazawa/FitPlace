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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'twitter' => [
        "client_id" => env("TWITTER_AUTH_CLIENT_ID"),
        "client_secret" => env("TWITTER_AUTH_CLIENT_SECRET"),
        "redirect" => env("TWITTER_CALLBACK_URL"),
    ],

    'line_notify' => [
        'client_id' => env('LINE_NOTIFY_AUTH_CLIENT_ID'),
        'client_secret' => env('LINE_NOTIFY_AUTH_CLIENT_SECRET'),
        'redirect' => env('LINE_NOTIFY_CALLBACK_URL'),
    ]

];
