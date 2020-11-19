<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\View;

class PbControllerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        // Move assets folder to the public disk
        /* $storage = storage_path();
        $assets = __DIR__.'/assets';
        shell_exec("mv -v ".$assets." ".$storage."/public/"); */
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