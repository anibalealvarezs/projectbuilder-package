<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;

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
        $this->loadViewsFrom($views, PbHelpers::PB_PREFIX);
        // Publish
        // All
        $allPublish = [
            $dirs['components'] => resource_path('js/Pages/'.PbHelpers::PB_PACKAGE),
            $dirs['assets_js'] => public_path('js/'.PbHelpers::PB_PACKAGE),
            $dirs['assets_css'] => public_path('css/'.PbHelpers::PB_PACKAGE),
            /* $dirs['core'] => resource_path('js'), */
        ];
        $this->publishes($allPublish, PbHelpers::PB_NAME.'-views');
        // Specific
        // Only Core Relacement
        $this->publishes([
            $dirs['core'] => resource_path('js'),
        ], PbHelpers::PB_NAME.'-core');
        // Only Components
        $this->publishes([
            $dirs['components'] => resource_path('js/Pages/'.PbHelpers::PB_PACKAGE),
        ], PbHelpers::PB_NAME.'-components');
        // Only Js Helpers
        $this->publishes([
            $dirs['assets_js'] => public_path('js/'.PbHelpers::PB_PACKAGE),
        ], PbHelpers::PB_NAME.'-js');
        // Only CSS
        $this->publishes([
            $dirs['assets_css'] => public_path('css/'.PbHelpers::PB_PACKAGE),
        ], PbHelpers::PB_NAME.'-css');
    }
}
