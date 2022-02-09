# Project Builder
[![Github tag](https://badgen.net/github/tag/anibalealvarezs/projectbuilder-package)](https://github.com/anibalealvarezs/projectbuilder-package/tags/) [![GitHub license](https://img.shields.io/github/license/anibalealvarezs/projectbuilder-package.svg)](https://github.com/anibalealvarezs/projectbuilder-package/blob/master/LICENSE) [![Github all releases](https://img.shields.io/github/downloads/anibalealvarezs/projectbuilder-package/total.svg)](https://github.com/anibalealvarezs/projectbuilder-package/releases/) [![GitHub latest commit](https://badgen.net/github/last-commit/anibalealvarezs/projectbuilder-package)](https://GitHub.com/anibalealvarezs/projectbuilder-package/commit/) [![Ask Me Anything !](https://img.shields.io/badge/Ask%20me-anything-1abc9c.svg)](https://github.com/anibalealvarezs/anibalealvarezs)

## About

This Laravel package is a simple and easy way to create a new project with a new Laravel application, based in the default Jestream + Inertia engine.

***

## Requirements

#### 0. Install Laravel 9.x and Jetstream/Inertia
```shell
composer create-project laravel/laravel my-project
```
Optionally, you can manually pre-install Jetstream/Inertia with teams. Project Builder will also ask you for it if you don't have it.
```shell
php artisan jetstream:install inertia --teams
```

#### 1. Add the repository to your composer.json
```json lines
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://satis.anibalalvarez.com"
        }
    ],
}
```

***

## Installation

#### 2. Require the package & install it
```shell
composer require anibalealvarezs/projectbuilder-package --no-cache
php artisan pbuilder:install --all
```
For package updating, you can use these equivalent options:
```shell
php artisan pbuilder:update
php artisan pbuilder:install --publish --migrate --seed --npm --compile
```
In case of Artisan commands failure, use the alternative commands:
```shell
php artisan pbuilder:altinstall
php artisan pbuilder:altupdate
```
For full command information, you can check the help command:
```shell
php artisan pbuilder:help
```
In case of links failure (if "pbstorage" links show error), navigate to ```/public``` folder, manually delete the link, and create a new one with the following command:
```shell
ln -s ../vendor/anibalealvarezs/projectbuilder-package/src/assets pbstorage
```

***

## Last steps...

#### 3. Comment/remove the default routes in ```/routes/web.php```
```php
/*
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
*/
```
