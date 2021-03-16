<?php

use Anibalealvarezs\Projectbuilder\Controllers\User\PbUserController as UserController;

Route::resource('users', UserController::class)->name('*', 'users');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});