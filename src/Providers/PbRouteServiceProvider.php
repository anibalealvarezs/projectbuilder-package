<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Support\ServiceProvider;

class PbRouteServiceProvider extends ServiceProvider
{
    protected string $namespace = PbHelpers::PB_VENDOR . '\\' . PbHelpers::PB_PACKAGE . '\\Controllers';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Routes
        include __DIR__ . '/../Routes/web.php';
        include __DIR__ . '/../Routes/api.php';
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
