<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;

trait PbServiceProviderTrait {

    protected function booter()
    {
        $dirs = [
            'core' => __DIR__ . '/../../resources/core',
            'components' => __DIR__ . '/../../resources/js',
            'assets_js' => __DIR__ . '/../../src/assets/js',
            'assets_css' => __DIR__ . '/../../src/assets/css',
        ];
        // Views
        $views = __DIR__ . '/../../resources/js';
        $this->loadViewsFrom($views, AeasHelpers::AEAS_PREFIX);
        // Publish
        // All
        $allPublish = [
            $dirs['components'] => resource_path('js/Pages/'.AeasHelpers::AEAS_PACKAGE),
            $dirs['assets_js'] => public_path('js/'.AeasHelpers::AEAS_PACKAGE),
            $dirs['assets_css'] => public_path('css/'.AeasHelpers::AEAS_PACKAGE),
            /* $dirs['core'] => resource_path('js'), */
        ];
        $this->publishes($allPublish, AeasHelpers::AEAS_NAME.'-views');
        // Specific
        // Only Core Relacement
        $this->publishes([
            $dirs['core'] => resource_path('js'),
        ], AeasHelpers::AEAS_NAME.'-core');
        // Only Components
        $this->publishes([
            $dirs['components'] => resource_path('js/Pages/'.AeasHelpers::AEAS_PACKAGE),
        ], AeasHelpers::AEAS_NAME.'-components');
        // Only Js Helpers
        $this->publishes([
            $dirs['assets_js'] => public_path('js/'.AeasHelpers::AEAS_PACKAGE),
        ], AeasHelpers::AEAS_NAME.'-js');
        // Only CSS
        $this->publishes([
            $dirs['assets_css'] => public_path('css/'.AeasHelpers::AEAS_PACKAGE),
        ], AeasHelpers::AEAS_NAME.'-css');
    }
}
