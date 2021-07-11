<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

trait PbServiceProviderTrait {

    protected function booter($name, $package)
    {
        $dirs = [
            'core' => __DIR__ . '/../../resources/core',
            'components' => __DIR__ . '/../../resources/js',
            'assets_js' => __DIR__ . '/../../src/assets/js',
            'assets_css' => __DIR__ . '/../../src/assets/css',
        ];
        // Views
        $views = __DIR__ . '/../../resources/views';
        $this->loadViewsFrom($views, $package);
        // Publish
        // All
        $allPublish = [
            $dirs['components'] => resource_path('js/Pages/'.$package),
            $dirs['assets_js'] => public_path('js/'.$package),
            $dirs['assets_css'] => public_path('css/'.$package),
            /* $dirs['core'] => resource_path('js'), */
        ];
        $this->publishes($allPublish, $name.'-views');
        // Specific
        // Only Core Relacement
        $this->publishes([
            $dirs['core'] => resource_path('js'),
        ], $name.'-core');
        // Only Components
        $this->publishes([
            $dirs['components'] => resource_path('js/Pages/'.$package),
        ], $name.'-components');
        // Only Js Helpers
        $this->publishes([
            $dirs['assets_js'] => public_path('js/'.$package),
        ], $name.'-js');
        // Only CSS
        $this->publishes([
            $dirs['assets_css'] => public_path('css/'.$package),
        ], $name.'-css');
    }
}
