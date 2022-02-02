<?php

use Anibalealvarezs\Projectbuilder\Controllers\Config\PbLocaleController as LocaleController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Inertia\Inertia;

(new PbHelpers())->buildCrudRoutes('web');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware([
    ...PbHelpers::getDefaultGroupsMiddlewares()['web'],
    ...PbHelpers::getDefaultGroupsMiddlewares()['auth'],
])->name('dashboard');

Route::post('/locale', [LocaleController::class, 'update'])->middleware([
    ...PbHelpers::getDefaultGroupsMiddlewares()['web'],
    ...PbHelpers::getDefaultGroupsMiddlewares()['auth'],
])->name('locale');

Route::get(config('pbuilder.secretlogin'), fn() => Inertia::render(PbHelpers::getDefault('package').'/Auth/Login', [
    'canResetPassword' => Route::has('password.request') && !PbConfig::getValueByKey('_DISABLE_PASSWORD_RESET_'),
    'status' => session('status'),
    'loginEnabled' => true,
]))->middleware(PbHelpers::getDefaultGroupsMiddlewares()['web'])->name('secretlogin');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Route::get('root', function () {
    return PbHelpers::getWelcomeRoute();
})->middleware(PbHelpers::getDefaultGroupsMiddlewares()['web'])->name('root');

Route::get('/', function () {
    return PbHelpers::getWelcomeRoute();
})->middleware(PbHelpers::getDefaultGroupsMiddlewares()['web']);
