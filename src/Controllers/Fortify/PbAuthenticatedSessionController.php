<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Fortify;

use Anibalealvarezs\Projectbuilder\Actions\Session\PbCheckSingleSessionOnLogin;
use Anibalealvarezs\Projectbuilder\Actions\Session\PbSaveUserNewSessionData;
use Anibalealvarezs\Projectbuilder\Actions\Session\PbUpdateCurrentLocale;
use Illuminate\Routing\Pipeline;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\LoginRequest;

class PbAuthenticatedSessionController extends AuthenticatedSessionController
{
    /**
     * Get the authentication pipeline instance.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Pipeline\Pipeline
     */
    protected function loginPipeline(LoginRequest $request)
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
            PbCheckSingleSessionOnLogin::class,
            PbSaveUserNewSessionData::class,
            PbUpdateCurrentLocale::class,
        ]));
    }
}
