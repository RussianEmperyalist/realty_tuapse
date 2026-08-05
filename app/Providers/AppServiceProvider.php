<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Timeweb App Platform terminates TLS at the edge and forwards plain
        // HTTP to the container, so force https for generated URLs in prod.
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
