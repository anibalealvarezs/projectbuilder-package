<?php

namespace Anibalealvarezs\Projectbuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;

class ProjectbuilderServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        //global middleware
        $kernel->prependMiddleware(Middleware\ProjectbuilderHttpsMiddleware::class);
        $kernel->pushMiddleware(Middleware\ProjectbuilderHttpsMiddleware::class);
        //router middleware
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', Middleware\ProjectbuilderHttpsMiddleware::class);
        include __DIR__ . '/routes.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\ProjectbuilderController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Middleware\ProjectbuilderHttpsMiddleware');
        $this->loadViewsFrom(__DIR__.'/views', 'builder');
    }
}