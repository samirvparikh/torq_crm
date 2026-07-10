<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IndiaMART API Credentials
    |--------------------------------------------------------------------------
    |
    | Credentials can be overridden at runtime via the Settings module.
    | Environment variables serve as defaults for initial setup.
    |
    */

    'api_key' => env('INDIAMART_API_KEY'),
    'glusr_id' => env('INDIAMART_GLUSR_ID'),
    'access_token' => env('INDIAMART_ACCESS_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Sync Configuration
    |--------------------------------------------------------------------------
    */

    'auto_sync' => env('INDIAMART_AUTO_SYNC', true),
    'sync_interval' => (int) env('INDIAMART_SYNC_INTERVAL', 30),
    'api_timeout' => (int) env('INDIAMART_API_TIMEOUT', 30),
    'retry_attempts' => (int) env('INDIAMART_RETRY_ATTEMPTS', 3),

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    */

    'base_url' => env('INDIAMART_BASE_URL', 'https://mapi.indiamart.com/wservce/crm/crmListing/v2/'),

];
