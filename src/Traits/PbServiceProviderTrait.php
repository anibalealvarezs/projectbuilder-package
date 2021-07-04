<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;

trait PbServiceProviderTrait {

    protected $aeas;

    protected function booter()
    {
        $this->aeas = new AeasHelpers();
        $dirs = [
            'core' => __DIR__ . '/../../resources/core',
            'components' => __DIR__ . '/../../resources/js',
            'assets_js' => __DIR__ . '/../../src/assets/js',
            'assets_css' => __DIR__ . '/../../src/assets/css',
        ];
        // Views
        $views = __DIR__ . '/../../resources/js';
        $this->loadViewsFrom($views, $this->aeas->prefix);
        // Publish
        // All
        $allPublish = [
            $dirs['components'] => resource_path('js/Pages/'.$this->aeas->package),
            $dirs['assets_js'] => public_path('js/'.$this->aeas->package),
            $dirs['assets_css'] => public_path('css/'.$this->aeas->package),
            /* $dirs['core'] => resource_path('js'), */
        ];
        $this->publishes($allPublish, $this->aeas->name.'-views');
        // Specific
        // Only Core Relacement
        $this->publishes([
            $dirs['core'] => resource_path('js'),
        ], $this->aeas->name.'-core');
        // Only Components
        $this->publishes([
            $dirs['components'] => resource_path('js/Pages/'.$this->aeas->package),
        ], $this->aeas->name.'-components');
        // Only Js Helpers
        $this->publishes([
            $dirs['assets_js'] => public_path('js/'.$this->aeas->package),
        ], $this->aeas->name.'-js');
        // Only CSS
        $this->publishes([
            $dirs['assets_css'] => public_path('css/'.$this->aeas->package),
        ], $this->aeas->name.'-css');
    }
}
