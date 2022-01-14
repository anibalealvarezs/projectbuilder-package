<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
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
        $this->app->config["filesystems.disks." . strtolower(PbHelpers::getDefault('prefix'))] = [
            'driver' => 'local',
            'root' => __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets',
            'url' => config('app.url') . DIRECTORY_SEPARATOR . PbHelpers::getDefault('storageDirName'),
            'visibility' => 'public',
        ];
        if (session('locale')) {
            $this->app->setLocale(session('locale'));
        }
    }
}
