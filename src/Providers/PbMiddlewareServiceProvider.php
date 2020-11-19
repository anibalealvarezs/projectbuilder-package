<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\View;

class PbMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        //global middleware
        $kernel->prependMiddleware('Anibalealvarezs\Projectbuilder\Middleware\PbHttpsMiddleware');
        $kernel->pushMiddleware('Anibalealvarezs\Projectbuilder\Middleware\PbHttpsMiddleware');
        $kernel->prependMiddleware('Anibalealvarezs\Projectbuilder\Middleware\PbSingleSession');
        $kernel->pushMiddleware('Anibalealvarezs\Projectbuilder\Middleware\PbSingleSession');
        //router middleware
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', 'Anibalealvarezs\Projectbuilder\Middleware\PbHttpsMiddleware');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Anibalealvarezs\Projectbuilder\Middleware\PbHttpsMiddleware');
    }
}