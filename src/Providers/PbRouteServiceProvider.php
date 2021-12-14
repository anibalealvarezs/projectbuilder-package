<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Support\ServiceProvider;

class PbRouteServiceProvider extends ServiceProvider
{
    protected $namespace ='Anibalealvarezs\Projectbuilder\Controllers';

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
        include __DIR__ . '/../Routes/inertia.php';
        include __DIR__ . '/../Routes/livewire.php';
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
