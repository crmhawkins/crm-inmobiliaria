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

    'google' => [
        'maps_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'idealista' => [
        'client_id' => env('IDEALISTA_CLIENT_ID'),
        'client_secret' => env('IDEALISTA_CLIENT_SECRET'),
        'basic_token' => env('IDEALISTA_BASIC_TOKEN'),
        'feed_key' => env('IDEALISTA_FEED_KEY'),
        'scope' => env('IDEALISTA_SCOPE', 'read'),
        'host_template' => env('IDEALISTA_HOST_TEMPLATE', 'https://partners-sandbox.idealista.%s'),
        'country' => env('IDEALISTA_COUNTRY', 'com'),
        'timeout' => env('IDEALISTA_TIMEOUT', 15),
        'verify_ssl' => env('IDEALISTA_VERIFY_SSL', true),
    ],

];
