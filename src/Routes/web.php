<?php

use Anibalealvarezs\Projectbuilder\Controllers\Navigation\PbNavigationController as NavigationController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;
use Illuminate\Foundation\Application;
use Inertia\Inertia;

$models = [
    /* object => Directory*/
    'user' => 'User',
    'config' => 'Config',
    'navigation' => 'Navigation',
    'role' => 'Permission',
    'permission' => 'Permission',
    'logger' => 'Logger'
];

foreach($models as $model => $directory) {
    $class = '\\Anibalealvarezs\\Projectbuilder\\Controllers\\'.$directory.'\\Pb'.ucfirst($model).'Controller';
    Route::resource($model.'s', $class)->middleware(['web', 'auth:sanctum', 'verified'])->name('*', $model.'s');
}

Route::group(['middleware' => ['web', 'auth:sanctum', 'verified']], function () {
    Route::prefix('navigations')->group(function () {
        Route::post('/sort/{id}', [NavigationController::class, 'sort']);
    });
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['web', 'auth:sanctum', 'verified'])->name('dashboard');

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
