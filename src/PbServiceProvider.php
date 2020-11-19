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
        $views = __DIR__.'/views';
        $this->loadViewsFrom($views, 'builder');
        // View Composers
        View::composers([
            'Anibalealvarezs\Projectbuilder\ViewComposers\ScriptsComposer' => ['builder::layouts.front.resources.scripts'],
            'Anibalealvarezs\Projectbuilder\ViewComposers\StylesComposer' => ['builder::layouts.front.resources.styles']
        ]);
        // Move assets folder to the public disk
        $storage = storage_path();
        $assets = __DIR__.'/assets';
        shell_exec("mv -v ".$assets." ".$storage."/public/");

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Calculator\PbCalculatorController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Permission\PbPermissionController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Permission\PbRoleController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\User\PbUserController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbForgotPasswordController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbLoginController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbRegisterController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Controllers\Auth\PbResetPasswordController');
        $this->app->make('Anibalealvarezs\Projectbuilder\Middleware\PbHttpsMiddleware');
    }
}