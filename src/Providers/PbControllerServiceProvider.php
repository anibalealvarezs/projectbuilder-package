<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class PbControllerServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected string $namespace;
    /**
     * @var string
     */
    protected string $prefix;
    /**
     * @var string
     */
    private string $suffix;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->namespace = app(PbUtilities::class)->vendor.'\\'.app(PbUtilities::class)->package.'\Controllers';
        $this->prefix = app(PbUtilities::class)->prefix;
        $this->suffix = 'Controller';
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function boot(Kernel $kernel)
    {
        if (Schema::hasTable('modules')) {
            $models = PbModule::whereIn('modulekey', app(PbUtilities::class)->modulekeys)->pluck('modulekey');
            foreach ($models as $model) {
                if (class_exists($this->namespace.'\\'.ucfirst($model).'\\'.$this->prefix.ucfirst($model).$this->suffix)) {
                    $this->app->make($this->namespace.'\\'.ucfirst($model).'\\'.$this->prefix.ucfirst($model).$this->suffix);
                } elseif (class_exists('App\\Http\\Controllers\\'.ucfirst($model).$this->suffix)) {
                    $this->app->make('App\\Http\\Controllers\\'.ucfirst($model).$this->suffix);
                }
            }
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
