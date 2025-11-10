<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tatum API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Tatum API integration including API keys, base URLs,
    | and headers for API requests.
    |
    */

    'api_key' => env('TATUM_API_KEY', 't-68ad501c796ef2921a0978d2-b0b183081e7449cfbcd9d531'),

    'base_url_v3' => env('TATUM_BASE_URL_V3', 'https://api.tatum.io/v3'),

    'base_url_v4' => env('TATUM_BASE_URL_V4', 'https://api.tatum.io/v4'),

    'headers' => [
        'accept' => 'application/json',
        'x-api-key' => env('TATUM_API_KEY', 't-68ad501c796ef2921a0978d2-b0b183081e7449cfbcd9d531'),
    ],

];

