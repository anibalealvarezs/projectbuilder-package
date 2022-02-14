<?php

use Anibalealvarezs\Projectbuilder\Controllers\Config\PbLocaleController as LocaleController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;
use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Inertia\Inertia;

(new PbUtilities())->buildCrudRoutes('web');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware([
    ...getDefaultGroupsMiddlewares('web'),
    ...getDefaultGroupsMiddlewares('auth'),
    ...getDefaultGroupsMiddlewares('debug'),
])->name('dashboard');

Route::post('/locale', [LocaleController::class, 'update'])->middleware([
    ...getDefaultGroupsMiddlewares('web'),
    ...getDefaultGroupsMiddlewares('auth'),
    ...getDefaultGroupsMiddlewares('debug'),
])->name('locale');

Route::get(config('pbuilder.secretlogin'), fn() => Inertia::render(app(PbUtilities::class)->package.'/Auth/Login', [
    'canResetPassword' => Route::has('password.request') && !getConfigValue('_DISABLE_PASSWORD_RESET_'),
    'status' => session('status'),
    'loginEnabled' => true,
]))->middleware([
    ...getDefaultGroupsMiddlewares('web'),
    ...getDefaultGroupsMiddlewares('debug'),
])->name('secretlogin');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Route::get('root', function () {
    return welcomeRoute();
})->middleware([
    ...getDefaultGroupsMiddlewares('web'),
    ...getDefaultGroupsMiddlewares('debug'),
])->name('root');

Route::get('/', function () {
    return welcomeRoute();
})->middleware([
    ...getDefaultGroupsMiddlewares('web'),
    ...getDefaultGroupsMiddlewares('debug'),
]);
