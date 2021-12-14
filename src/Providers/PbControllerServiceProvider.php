<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PbControllerServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $namespace;
    /**
     * @var string
     */
    protected $prefix;
    /**
     * @var string
     */
    private $suffix;

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
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'ForgotPassword'.$this->suffix);
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'Login'.$this->suffix);
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'Register'.$this->suffix);
        $this->app->make($this->namespace.'\Auth\\'.$this->prefix.'ResetPassword'.$this->suffix);
        $this->app->make($this->namespace.'\Config\\'.$this->prefix.'Config'.$this->suffix);
        $this->app->make($this->namespace.'\Dashboard\\'.$this->prefix.'Dashboard'.$this->suffix);
        $this->app->make($this->namespace.'\User\\'.$this->prefix.'User'.$this->suffix);
        $this->app->make($this->namespace.'\Logger\\'.$this->prefix.'Logger'.$this->suffix);
        $this->app->make($this->namespace.'\Permission\\'.$this->prefix.'Permission'.$this->suffix);
        $this->app->make($this->namespace.'\Permission\\'.$this->prefix.'Role'.$this->suffix);
    }
}
