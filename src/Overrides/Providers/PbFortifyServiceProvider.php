<?php

namespace Anibalealvarezs\Projectbuilder\Overrides\Providers;

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\FortifyServiceProvider;

class PbFortifyServiceProvider extends FortifyServiceProvider
{
    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (Fortify::$registersRoutes) {
            Route::group([
                'namespace' => 'Laravel\Fortify\Http\Controllers',
                'domain' => config('fortify.domain', null),
                'prefix' => config('fortify.prefix'),
            ], function () {
                $this->loadRoutesFrom(base_path().'/vendor/laravel/fortify/routes/routes.php');
                $this->loadRoutesFrom(__DIR__ . '/../Routes/fortify.php');
            });
        }
    }
}
