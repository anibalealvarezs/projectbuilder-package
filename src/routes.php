<?php

use Anibalealvarezs\Projectbuilder\Controllers\User\PbUserController as UserController;
use Anibalealvarezs\Projectbuilder\Controllers\Config\PbConfigController as ConfigController;
use Anibalealvarezs\Projectbuilder\Controllers\Navigation\PbNavigationController as NavigationController;

Route::resource('users', UserController::class)->middleware(['web', 'auth'])->name('*', 'users');
Route::resource('configs', ConfigController::class)->middleware(['web', 'auth'])->name('*', 'configs');
Route::resource('navigations', NavigationController::class)->middleware(['web', 'auth'])->name('*', 'navigations');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
