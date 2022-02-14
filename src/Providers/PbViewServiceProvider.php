<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Traits\PbServiceProviderTrait;
use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Illuminate\Support\ServiceProvider;

class PbViewServiceProvider extends ServiceProvider
{
    use PbServiceProviderTrait;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $dir = app(PbUtilities::class)->directory;
        $dirs = [
            'core' => __DIR__ . '/../../../'.$dir.'/resources/core',
            'components' => __DIR__ . '/../../../'.$dir.'/resources/js',
            'assets_js' => __DIR__ . '/../../../'.$dir.'/src/assets/js',
            'assets_css' => __DIR__ . '/../../../'.$dir.'/src/assets/css',
            'assets_fonts' => __DIR__ . '/../../../'.$dir.'/src/assets/fonts',
            'assets_img' => __DIR__ . '/../../../'.$dir.'/src/assets/img',
            'blade' => __DIR__ . '/../../../'.$dir.'/resources/views',
            'config' => __DIR__ . '/../../../'.$dir.'/src/config',
        ];
        $this->booter(app(PbUtilities::class)->prefix, app(PbUtilities::class)->package, $dirs);
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
