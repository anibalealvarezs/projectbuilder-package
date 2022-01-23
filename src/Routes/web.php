<?php

use Anibalealvarezs\Projectbuilder\Controllers\Config\PbLocaleController as LocaleController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;
use Anibalealvarezs\Projectbuilder\Helpers\PbHelpers;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

(new PbHelpers())->buildCrudRoutes('web');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['web', 'auth:sanctum', 'verified', 'set_locale'])->name('dashboard');

Route::post('/locale', [LocaleController::class, 'update'])->middleware(['web'])->name('locale');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
