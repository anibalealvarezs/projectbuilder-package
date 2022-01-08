<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbModule;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
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

        $this->namespace = PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Controllers';
        $this->prefix = PbHelpers::PB_PREFIX;
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
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'ForgotPassword'.$this->suffix);
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'Login'.$this->suffix);
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'Register'.$this->suffix);
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'ResetPassword'.$this->suffix);
        $models = [...PbHelpers::NON_EXISTENT_MODULES, ...PbModule::pluck('modulekey')];
        foreach ($models as $model) {
            $this->app->make($this->namespace.'\\'.ucfirst($model).'\\'.$this->prefix.ucfirst($model).$this->suffix);
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
