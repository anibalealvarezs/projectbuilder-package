<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
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
        $this->booter(PbHelpers::PB_NAME, PbHelpers::PB_PACKAGE, PbHelpers::PB_DIR);
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
