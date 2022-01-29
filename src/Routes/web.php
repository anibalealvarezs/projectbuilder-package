<?php

use Anibalealvarezs\Projectbuilder\Controllers\Config\PbLocaleController as LocaleController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Anibalealvarezs\Projectbuilder\Models\PbConfig;
use Inertia\Inertia;

(new PbHelpers())->buildCrudRoutes('web');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['web', 'auth:sanctum', 'verified', 'set_locale', 'single_session'])->name('dashboard');

Route::post('/locale', [LocaleController::class, 'update'])->middleware(['web', 'auth:sanctum', 'verified', 'single_session'])->name('locale');

Route::get(config('pbuilder.secretlogin'), fn() => Inertia::render(PbHelpers::getDefault('package').'/Auth/Login', [
    'canResetPassword' => Route::has('password.request') && !PbConfig::getValueByKey('_DISABLE_PASSWORD_RESET_'),
    'status' => session('status'),
    'loginEnabled' => true,
]))->middleware(['web'])->name('secretlogin');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Route::get('root', function () {
    return PbHelpers::getWelcomeRoute();
})->name('root');

Route::get('/', function () {
    return PbHelpers::getWelcomeRoute();
});
