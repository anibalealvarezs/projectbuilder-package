<?php

use Anibalealvarezs\Projectbuilder\Controllers\Fortify\PbAuthenticatedSessionController;
use Anibalealvarezs\Projectbuilder\Controllers\Fortify\PbProfileInformationController;
use Anibalealvarezs\Projectbuilder\Controllers\Fortify\PbTwoFactorAuthenticatedSessionController;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::group(['middleware' => PbHelpers::getDefaultGroupsMiddlewares()['web']], function () {
    $enableViews = config('fortify.views', true);

    // Authentication...
    if ($enableViews) {
        Route::get('/login', [PbAuthenticatedSessionController::class, 'create'])
            ->middleware(['guest:'.config('fortify.guard')])
            ->name('login');
    }

    $limiter = config('fortify.limiters.login');
    $twoFactorLimiter = config('fortify.limiters.two-factor');

    Route::post('/login', [PbAuthenticatedSessionController::class, 'store'])
        ->middleware(array_filter([
            'guest:'.config('fortify.guard'),
            $limiter ? 'throttle:'.$limiter : null,
        ]));

    Route::post('/logout', [PbAuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Profile Information...
    if (Features::enabled(Features::updateProfileInformation())) {
        Route::put('/user/profile-information', [PbProfileInformationController::class, 'update'])
            ->middleware([...[config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')], ...PbHelpers::getDefaultGroupsMiddlewares()['auth']])
            ->name('user-profile-information.update');
    }

    // Two Factor Authentication...
    if (Features::enabled(Features::twoFactorAuthentication())) {
        if ($enableViews) {
            Route::get('/two-factor-challenge', [PbTwoFactorAuthenticatedSessionController::class, 'create'])
                ->middleware(['guest:'.config('fortify.guard')])
                ->name('two-factor.login');
        }

        Route::post('/two-factor-challenge', [PbTwoFactorAuthenticatedSessionController::class, 'store'])
            ->middleware(array_filter([
                'guest:'.config('fortify.guard'),
                $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
            ]));
    }
});
