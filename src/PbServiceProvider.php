<?php

namespace Anibalealvarezs\Projectbuilder;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;

class PbServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        //global middleware
        $kernel->prependMiddleware(Middleware\PbHttpsMiddleware::class);
        $kernel->pushMiddleware(Middleware\PbHttpsMiddleware::class);
        $kernel->prependMiddleware(Middleware\PbSingleSession::class);
        $kernel->pushMiddleware(Middleware\PbSingleSession::class);
        //router middleware
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', Middleware\PbHttpsMiddleware::class);
        // Routes
        include __DIR__ . '/routes.php';
        // Migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        // Views
        $views = resource_path(__DIR__.'/views');
        $this->loadViewsFrom($views, 'builder');

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\PbController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\PbPermissionController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\PbRoleController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\PbUserController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbForgotPasswordController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbLoginController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbRegisterController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbResetPasswordController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Middleware\PbHttpsMiddleware');
    }
}