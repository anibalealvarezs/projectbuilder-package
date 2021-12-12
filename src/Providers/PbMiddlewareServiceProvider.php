<?php

namespace Anibalealvarezs\Projectbuilder\Providers;

use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
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
        $kernel->prependMiddleware(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'HttpsMiddleware');
        $kernel->pushMiddleware(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'HttpsMiddleware');
        $kernel->prependMiddleware(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'SingleSessionMiddleware');
        $kernel->pushMiddleware(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'SingleSessionMiddleware');
        //router middleware
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'HttpsMiddleware');
        $router->aliasMiddleware('role_or_permission', PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'RoleOrPermissionMiddleware');
        $router->aliasMiddleware('is_user_viewable', PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'IsUserViewableMiddleware');
        $router->aliasMiddleware('is_user_editable', PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'IsUserEditableMiddleware');
        $router->aliasMiddleware('is_user_selectable', PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'IsUserSelectableMiddleware');
        $router->aliasMiddleware('is_user_deletable', PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'IsUserDeletableMiddleware');
        $router->aliasMiddleware('api_access', PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'CanAccessApiMiddleware');
    }

    /**
     * Register the application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->app->make(PbHelpers::PB_VENDOR.'\\'.PbHelpers::PB_PACKAGE.'\Middleware\\'.PbHelpers::PB_PREFIX.'HttpsMiddleware');
    }
}
