<?php

use Anibalealvarezs\Projectbuilder\Controllers\User\PbUserController as UserController;
use Anibalealvarezs\Projectbuilder\Controllers\Config\PbConfigController as ConfigController;
use Anibalealvarezs\Projectbuilder\Controllers\Navigation\PbNavigationController as NavigationController;
use Anibalealvarezs\Projectbuilder\Controllers\Permission\PbRoleController as RoleController;
use Anibalealvarezs\Projectbuilder\Controllers\Permission\PbPermissionController as PermissionController;
use Anibalealvarezs\Projectbuilder\Controllers\Dashboard\PbDashboardController as DashboardController;

Route::resource('users', UserController::class)->middleware(['web', 'auth:sanctum', 'verified'])->name('*', 'users');
Route::resource('configs', ConfigController::class)->middleware(['web', 'auth:sanctum', 'verified'])->name('*', 'configs');
Route::resource('navigations', NavigationController::class)->middleware(['web', 'auth:sanctum', 'verified'])->name('*', 'navigations');
Route::resource('roles', RoleController::class)->middleware(['web', 'auth:sanctum', 'verified'])->name('*', 'roles');
Route::resource('permissions', PermissionController::class)->middleware(['web', 'auth:sanctum', 'verified'])->name('*', 'permissions');

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

Route::prefix('api')->group(function () {
    Route::resource('users', UserController::class)->middleware(['auth:sanctum'])->name('*', 'users');
    Route::resource('configs', ConfigController::class)->middleware(['auth:sanctum'])->name('*', 'configs');
    Route::resource('navigations', NavigationController::class)->middleware(['auth:sanctum'])->name('*', 'navigations');
    Route::resource('roles', RoleController::class)->middleware(['auth:sanctum'])->name('*', 'roles');
    Route::resource('permissions', PermissionController::class)->middleware(['auth:sanctum'])->name('*', 'permissions');
});
