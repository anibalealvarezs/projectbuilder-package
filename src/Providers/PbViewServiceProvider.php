<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Anibalealvarezs\Projectbuilder\Traits\PbServiceProviderTrait;
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
        $helper = new AeasHelpers();
        $this->booter($helper);
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
