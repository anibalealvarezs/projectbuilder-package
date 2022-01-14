<?php

namespace Anibalealvarezs\Projectbuilder\Controllers\Fortify;

use Anibalealvarezs\Projectbuilder\Actions\Session\PbUpdateCurrentLocale;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticatedSessionController;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;

class PbTwoFactorAuthenticatedSessionController extends TwoFactorAuthenticatedSessionController
{
    /**
     * Attempt to authenticate a new session using the two factor authentication code.
     *
     * @param  TwoFactorLoginRequest  $request
     * @return mixed
     */
    public function store(TwoFactorLoginRequest $request)
    {
        return app(PbUpdateCurrentLocale::class)->handle($request, app(parent::store()));
    }
}
