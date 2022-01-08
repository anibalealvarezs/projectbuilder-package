<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PbConfigServiceProvider extends ServiceProvider
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
        $this->app->config["filesystems.disks.pb"] = [
            'driver' => 'local',
            'root' => __DIR__ . '/../assets',
            'url' => env('APP_URL').'/pbstorage',
            'visibility' => 'public',
        ];
    }
}
