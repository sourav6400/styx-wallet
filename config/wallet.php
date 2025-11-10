<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Wallet Application Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for wallet application settings including webhook URLs
    | and application domain.
    |
    */

    'app_url' => env('WALLET_APP_URL', 'https://web.styxwallet.com'),

    'webhook_url' => env('WALLET_WEBHOOK_URL') ?? rtrim(env('WALLET_APP_URL', 'https://web.styxwallet.com'), '/') . '/transaction-alert',

];

