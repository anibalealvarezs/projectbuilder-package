<?php

use Anibalealvarezs\Projectbuilder\Controllers\Jetstream\PbApiTokenController;
use Anibalealvarezs\Projectbuilder\Controllers\Jetstream\PbUserProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Jetstream\Jetstream;

Route::group(['middleware' => config('jetstream.middleware', ['web'])], function () {

    Route::group(['middleware' => ['auth', 'verified']], function () {
        // User & Profile...
        Route::get('/user/profile', [PbUserProfileController::class, 'show'])
                    ->name('profile.show');

        // API...
        if (Jetstream::hasApiFeatures()) {
            Route::get('/user/api-tokens', [PbApiTokenController::class, 'index'])->name('api-tokens.index');
        }
    });
});
