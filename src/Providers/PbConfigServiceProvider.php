<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
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
        $this->app->config["filesystems.disks." . strtolower(app(PbUtilities::class)->prefix)] = [
            'driver' => 'local',
            'root' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets',
            'url' => config('app.url') . DIRECTORY_SEPARATOR . app(PbUtilities::class)->storageDirName,
            'visibility' => 'public',
        ];
        if (session('locale')) {
            $this->app->setLocale(session('locale'));
        }
    }
}
