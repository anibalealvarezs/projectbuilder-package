<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Models\PbCity;
use Anibalealvarezs\Projectbuilder\Models\PbCountry;
use Anibalealvarezs\Projectbuilder\Models\PbLanguage;
use Anibalealvarezs\Projectbuilder\Models\PbPermission;
use Anibalealvarezs\Projectbuilder\Models\PbUser;
use Anibalealvarezs\Projectbuilder\Observers\PbUserObserver;
use Anibalealvarezs\Projectbuilder\Observers\PbCityObserver;
use Anibalealvarezs\Projectbuilder\Observers\PbCountryObserver;
use Anibalealvarezs\Projectbuilder\Observers\PbLanguageObserver;
use Anibalealvarezs\Projectbuilder\Observers\PbPermissionObserver;
use Illuminate\Support\ServiceProvider;

class PbEventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        PbUser::observe(PbUserObserver::class);
        PbCity::observe(PbCityObserver::class);
        PbCountry::observe(PbCountryObserver::class);
        PbLanguage::observe(PbLanguageObserver::class);
        PbPermission::observe(PbPermissionObserver::class);
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
