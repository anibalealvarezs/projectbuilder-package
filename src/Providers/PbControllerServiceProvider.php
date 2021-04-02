<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
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
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $aeas = new AeasHelpers();
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Auth\\'.$aeas->prefix.'ForgotPasswordController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Auth\\'.$aeas->prefix.'LoginController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Auth\\'.$aeas->prefix.'RegisterController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Auth\\'.$aeas->prefix.'ResetPasswordController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Config\\'.$aeas->prefix.'ConfigController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Logger\\'.$aeas->prefix.'LoggerController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Permission\\'.$aeas->prefix.'PermissionController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Permission\\'.$aeas->prefix.'RoleController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\User\\'.$aeas->prefix.'UserController');
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Controllers\Config\\'.$aeas->prefix.'ConfigController');
    }
}
