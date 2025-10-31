<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

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
        // Force HTTPS if the request is behind a proxy (like load balancer) with HTTPS
        $isSecure = request()->header('X-Forwarded-Proto') === 'https' 
            || request()->header('X-Forwarded-Ssl') === 'on'
            || request()->secure();
            
        if ($isSecure) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
            
            // Ensure session cookies are secure for HTTPS on cloud deployments
            if (Config::get('session.secure') === null) {
                Config::set('session.secure', true);
            }
        }
    }
}
