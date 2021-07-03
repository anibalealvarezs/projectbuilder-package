<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\AeasHelpers as AeasHelpers;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;

class PbMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(\Illuminate\Contracts\Http\Kernel $kernel)
    {
        $aeas = new AeasHelpers();
        //global middleware
        $kernel->prependMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
        $kernel->pushMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
        $kernel->prependMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'SingleSessionMiddleware');
        $kernel->pushMiddleware($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'SingleSessionMiddleware');
        //router middleware
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', $aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
        $router->aliasMiddleware('role_or_permission', $aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'RoleOrPermissionMiddleware');
        $router->aliasMiddleware('is_user_viewable', $aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'IsUserViewableMiddleware');
        $router->aliasMiddleware('is_user_editable', $aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'IsUserEditableMiddleware');
        $router->aliasMiddleware('is_user_selectable', $aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'IsUserSelectableMiddleware');
        $router->aliasMiddleware('is_user_deletable', $aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'IsUserDeletableMiddleware');
    }

    /**
     * Register the application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $aeas = new AeasHelpers();
        $this->app->make($aeas->vendor.'\\'.$aeas->package.'\Middleware\\'.$aeas->prefix.'HttpsMiddleware');
    }
}
