<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Load wallet helper functions
        require_once app_path('Helpers/WalletHelper.php');

        // Force HTTPS if the request is behind a proxy (like ngrok) with HTTPS
        if (request()->header('X-Forwarded-Proto') === 'https' || request()->secure()) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }
        // if (config('app.env') == 'production') {
        //     URL::forceScheme('https');
        //     $this->app['request']->server->set('HTTPS', 'on');
        // }
    }
}
