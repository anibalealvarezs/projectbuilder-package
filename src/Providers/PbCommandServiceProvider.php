<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Commands\PbAltInstallCommand;
use Anibalealvarezs\Projectbuilder\Commands\PbAltUpdateCommand;
use Anibalealvarezs\Projectbuilder\Commands\PbHelpCommand;
use Anibalealvarezs\Projectbuilder\Commands\PbInstallCommand;
use Anibalealvarezs\Projectbuilder\Commands\PbUpdateCommand;
use Anibalealvarezs\Projectbuilder\Traits\PbServiceProviderTrait;
use Illuminate\Support\ServiceProvider;

class PbCommandServiceProvider extends ServiceProvider
{
    use PbServiceProviderTrait;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PbInstallCommand::class,
                PbHelpCommand::class,
                PbAltInstallCommand::class,
                PbUpdateCommand::class,
                PbAltUpdateCommand::class,
            ]);
        }
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
