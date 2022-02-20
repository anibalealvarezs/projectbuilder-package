<?php

use Anibalealvarezs\Projectbuilder\Controllers\Config\PbCacheController;
use Anibalealvarezs\Projectbuilder\Controllers\Config\PbLocaleController as LocaleController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;
use Anibalealvarezs\Projectbuilder\Facades\PbUtilitiesFacade;
use Anibalealvarezs\Projectbuilder\Utilities\PbUtilities;
use Inertia\Inertia;

app(PbUtilitiesFacade::class)::buildCrudRoutes('web');

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

Route::post('/clear-cache', [PbCacheController::class, 'clear'])->middleware([
    ...getDefaultGroupsMiddlewares('web'),
    ...getDefaultGroupsMiddlewares('auth'),
    ...getDefaultGroupsMiddlewares('debug'),
])->name('clear-cache');

Route::post('/clear-laravel-cache', [PbCacheController::class, 'clearLaravel'])->middleware([
    ...getDefaultGroupsMiddlewares('web'),
    ...getDefaultGroupsMiddlewares('auth'),
    ...getDefaultGroupsMiddlewares('debug'),
])->name('clear-laravel-cache');

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
