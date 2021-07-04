<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;

class PbMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(Kernel $kernel)
    {
        //global middleware
        $kernel->prependMiddleware(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'HttpsMiddleware');
        $kernel->pushMiddleware(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'HttpsMiddleware');
        $kernel->prependMiddleware(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'SingleSessionMiddleware');
        $kernel->pushMiddleware(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'SingleSessionMiddleware');
        //router middleware
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'HttpsMiddleware');
        $router->aliasMiddleware('role_or_permission', AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'RoleOrPermissionMiddleware');
        $router->aliasMiddleware('is_user_viewable', AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'IsUserViewableMiddleware');
        $router->aliasMiddleware('is_user_editable', AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'IsUserEditableMiddleware');
        $router->aliasMiddleware('is_user_selectable', AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'IsUserSelectableMiddleware');
        $router->aliasMiddleware('is_user_deletable', AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'IsUserDeletableMiddleware');
    }

    /**
     * Register the application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->app->make(AeasHelpers::AEAS_VENDOR.'\\'.AeasHelpers::AEAS_PACKAGE.'\Middleware\\'.AeasHelpers::AEAS_PREFIX.'HttpsMiddleware');
    }
}
