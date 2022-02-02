<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PbMiddlewareServiceProvider extends ServiceProvider
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

        $this->namespace = PbHelpers::getDefault('vendor').'\\'.PbHelpers::getDefault('package').'\Middleware';
        $this->prefix = $this->namespace.'\\'.PbHelpers::getDefault('prefix');
        $this->suffix = 'Middleware';
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        //global middleware
        $kernel->prependMiddleware($this->prefix.'Https'.$this->suffix);
        $kernel->pushMiddleware($this->prefix.'Https'.$this->suffix);
        //router middleware
        $router = $this->app['router'];
        $router->aliasMiddleware('role_or_permission', $this->prefix.'RoleOrPermission'.$this->suffix);
        $router->aliasMiddleware('is_user_viewable', $this->prefix.'IsUserViewable'.$this->suffix);
        $router->aliasMiddleware('is_user_editable', $this->prefix.'IsUserEditable'.$this->suffix);
        $router->aliasMiddleware('is_user_selectable', $this->prefix.'IsUserSelectable'.$this->suffix);
        $router->aliasMiddleware('is_user_deletable', $this->prefix.'IsUserDeletable'.$this->suffix);
        $router->aliasMiddleware('check_https', $this->prefix.'Https'.$this->suffix);
        $router->aliasMiddleware('is_debug_enabled', $this->prefix.'IsDebugModeEnabled'.$this->suffix);
        $router->aliasMiddleware('single_session', $this->prefix.'SingleSession'.$this->suffix);
        $router->aliasMiddleware('set_locale', $this->prefix.'SetLocale'.$this->suffix);
        $router->aliasMiddleware('can_access_api', $this->prefix.'CanAccessApi'.$this->suffix);
    }

    /**
     * Register the application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->app->make($this->prefix.'Https'.$this->suffix);
    }
}
