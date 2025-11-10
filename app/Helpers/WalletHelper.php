<?php

if (!function_exists('wallet_brand')) {
    /**
     * Get the wallet brand based on WALLET_APP_URL
     *
     * @return string 'styx' or 'ibex'
     */
    function wallet_brand(): string
    {
        $appUrl = config('wallet.app_url', 'https://web.styxwallet.com');
        return strpos($appUrl, 'ibexwallet.io') !== false ? 'ibex' : 'styx';
    }
}

if (!function_exists('wallet_logo_main')) {
    /**
     * Get the main logo path based on wallet brand
     *
     * @return string
     */
    function wallet_logo_main(): string
    {
        $brand = wallet_brand();
        return asset("images/logo/logo_main_{$brand}.svg");
    }
}

if (!function_exists('wallet_logo')) {
    /**
     * Get the logo path based on wallet brand
     *
     * @return string
     */
    function wallet_logo(): string
    {
        $brand = wallet_brand();
        return asset("images/logo/logo_{$brand}.png");
    }
}

if (!function_exists('wallet_favicon')) {
    /**
     * Get the favicon path based on wallet brand
     *
     * @return string
     */
    function wallet_favicon(): string
    {
        $brand = wallet_brand();
        return asset("images/favicon_{$brand}.ico");
    }
}

