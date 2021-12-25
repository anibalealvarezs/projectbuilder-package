<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\JetstreamServiceProvider;

class PbJetstreamServiceProvider extends JetstreamServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (Jetstream::$registersRoutes) {
            Route::group([
                'namespace' => 'Laravel\Jetstream\Http\Controllers',
                'domain' => config('jetstream.domain'),
                'prefix' => config('jetstream.prefix', config('jetstream.path')),
            ], function () {
                $this->loadRoutesFrom(base_path().'/vendor/laravel/jetstream/routes/'.config('jetstream.stack').'.php');
                $this->loadRoutesFrom(__DIR__ . '/../Routes/inertia.php');
                $this->loadRoutesFrom(__DIR__ . '/../Routes/livewire.php');
            });
        }
    }
}
