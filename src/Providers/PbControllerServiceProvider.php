<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
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
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Auth\\'.AeasHelpers::AEAS_PREFIX.'ForgotPasswordController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Auth\\'.AeasHelpers::AEAS_PREFIX.'LoginController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Auth\\'.AeasHelpers::AEAS_PREFIX.'RegisterController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Auth\\'.AeasHelpers::AEAS_PREFIX.'ResetPasswordController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Config\\'.AeasHelpers::AEAS_PREFIX.'ConfigController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Logger\\'.AeasHelpers::AEAS_PREFIX.'LoggerController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Permission\\'.AeasHelpers::AEAS_PREFIX.'PermissionController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Permission\\'.AeasHelpers::AEAS_PREFIX.'RoleController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\User\\'.AeasHelpers::AEAS_PREFIX.'UserController');
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Controllers\Dashboard\\'.AeasHelpers::AEAS_PREFIX.'DashboardController');
    }
}
