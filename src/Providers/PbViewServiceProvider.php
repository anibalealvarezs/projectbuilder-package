<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\View;

class PbViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        $aeas = new AeasHelpers();
        // Views
        $views = __DIR__ . '/../../resources/js';
        $this->loadViewsFrom($views, $aeas->prefix);
        $this->publishes([
            __DIR__ . '/../../resources/js' => resource_path('js/Pages/'.$aeas->package),
        ], $aeas->name.'-components');
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
