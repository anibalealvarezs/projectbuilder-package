<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Support\ServiceProvider;

class PbRouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Routes
        include __DIR__ . '/../routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
