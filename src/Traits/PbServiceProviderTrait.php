<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

trait PbServiceProviderTrait {

    protected function booter($name, $package, $dir)
    {
        $dirs = [
            'core' => __DIR__ . '/../../../'.$dir.'/resources/core',
            'components' => __DIR__ . '/../../../'.$dir.'/resources/js',
            'assets_js' => __DIR__ . '/../../../'.$dir.'/src/assets/js',
            'assets_css' => __DIR__ . '/../../../'.$dir.'/src/assets/css',
            'blade' => __DIR__ . '/../../../'.$dir.'/resources/views',
        ];
        // Views
        $views = resource_path('views/'.$package);
        $this->loadViewsFrom($views, $package);
        // Publish
        // All
        $allPublish = [
            $dirs['components'] => resource_path('js/Pages/'.$package),
            $dirs['assets_js'] => public_path('js/'.$package),
            $dirs['assets_css'] => public_path('css/'.$package),
            $dirs['blade'] => resource_path('views/'.$package),
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
        // Only Views
        $this->publishes([
            $dirs['blade'] => resource_path('views/'.$package),
        ], $name.'-blade');
        // Only CSS
        $this->publishes([
            $dirs['assets_css'] => public_path('css/'.$package),
        ], $name.'-css');
    }
}
