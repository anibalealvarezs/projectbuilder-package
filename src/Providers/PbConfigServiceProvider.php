<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\View;

class PbConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
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