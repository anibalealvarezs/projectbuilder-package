<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PbControllerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Auth\\'.PbHelpers::PB_PREFIX.'ForgotPasswordController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Auth\\'.PbHelpers::PB_PREFIX.'LoginController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Auth\\'.PbHelpers::PB_PREFIX.'RegisterController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Auth\\'.PbHelpers::PB_PREFIX.'ResetPasswordController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Config\\'.PbHelpers::PB_PREFIX.'ConfigController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Logger\\'.PbHelpers::PB_PREFIX.'LoggerController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Permission\\'.PbHelpers::PB_PREFIX.'PermissionController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Permission\\'.PbHelpers::PB_PREFIX.'RoleController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\User\\'.PbHelpers::PB_PREFIX.'UserController');
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers\Dashboard\\'.PbHelpers::PB_PREFIX.'DashboardController');
    }
}
