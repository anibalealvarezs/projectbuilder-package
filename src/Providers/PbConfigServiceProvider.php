<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PbConfigServiceProvider extends ServiceProvider
{
    private $configPath;

    public function __construct($app)
    {
        $this->configPath = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'config';

        parent::__construct($app);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        $this->publishes([
            $this->configPath.DIRECTORY_SEPARATOR.'pbuilder.php' => config_path('pbuilder.php')
        ], 'config');
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
