<?php

namespace Anibalealvarezs\Projectbuilder;

use Illuminate\Support\ServiceProvider;

class ProjectbuilderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Anibalealvarezs\Projectbuilder\ProjectbuilderController');
        $this->loadViewsFrom(__DIR__.'/views', 'builder');
    }
}