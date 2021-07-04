<?php

use Anibalealvarezs\Projectbuilder\Controllers\User\PbUserController as UserController;
use Anibalealvarezs\Projectbuilder\Controllers\Config\PbConfigController as ConfigController;
use Anibalealvarezs\Projectbuilder\Controllers\Navigation\PbNavigationController as NavigationController;
use Anibalealvarezs\Projectbuilder\Controllers\Permission\PbRoleController as RoleController;
use Anibalealvarezs\Projectbuilder\Controllers\Permission\PbPermissionController as PermissionController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;

Route::resource('users', UserController::class)->middleware(['web', 'auth'])->name('*', 'users');
Route::resource('configs', ConfigController::class)->middleware(['web', 'auth'])->name('*', 'configs');
Route::resource('navigations', NavigationController::class)->middleware(['web', 'auth'])->name('*', 'navigations');
Route::resource('roles', RoleController::class)->middleware(['web', 'auth'])->name('*', 'roles');
Route::resource('permissions', PermissionController::class)->middleware(['web', 'auth'])->name('*', 'permissions');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['web', 'auth'])->name('dashboard');

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
