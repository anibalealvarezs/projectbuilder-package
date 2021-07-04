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
        $dirs = [
            'core' => __DIR__ . '/../../resources/core',
            'components' => __DIR__ . '/../../resources/js',
            'assets_js' => __DIR__ . '/../../src/assets/js',
            'assets_css' => __DIR__ . '/../../src/assets/css',
        ];
        // Views
        $views = __DIR__ . '/../../resources/js';
        $this->loadViewsFrom($views, $aeas->prefix);
        // Publish
        // All
        $allPublish = [
            $dirs['components'] => resource_path('js/Pages/'.$aeas->package),
            $dirs['assets_js'] => public_path('js/'.$aeas->package),
            $dirs['assets_css'] => public_path('css/'.$aeas->package),
            /* $dirs['core'] => resource_path('js'), */
        ];
        $this->publishes($allPublish, $aeas->name.'-all');
        // Specific
        // Only Core Relacement
        $this->publishes([
            $dirs['core'] => resource_path('js'),
        ], $aeas->name.'-core');
        // Only Components
        $this->publishes([
            $dirs['components'] => resource_path('js/Pages/'.$aeas->package),
        ], $aeas->name.'-components');
        // Only Js Helpers
        $this->publishes([
            $dirs['assets_js'] => public_path('js/'.$aeas->package),
        ], $aeas->name.'-js');
        // Only CSS
        $this->publishes([
            $dirs['assets_css'] => public_path('css/'.$aeas->package),
        ], $aeas->name.'-css');
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
