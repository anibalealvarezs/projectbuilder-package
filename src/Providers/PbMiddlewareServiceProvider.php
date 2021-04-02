<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
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
        $aeas = new AeasHelpers();
        //global middleware
        $kernel->prependMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
        $kernel->pushMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
        $kernel->prependMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'SingleSession');
        $kernel->pushMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'SingleSession');
        //router middleware
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', $aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $aeas = new AeasHelpers();
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
    }
}
