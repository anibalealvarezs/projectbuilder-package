# Project Builder

***

## Requirements

#### 1. Add the repository and ignore autodiscover for jetstream and fortify packages in composer.json:
```json lines
"repositories": [
    {
        "type": "composer",
        "url": "https://satis.anibalalvarez.com"
    }
],
"extra": {
    "laravel": {
        "dont-discover": [
            "laravel/jetstream",
            "laravel/fortify"
        ]
    }
},
```

***

## Installation

#### 2. Require the package & install it
```shell
composer require anibalealvarezs/projectbuilder-package --no-cache
php artisan pbuilder:install --inertia
```
If Jetstream & Inertia already installed, remove the ```--inertia``` flag from the command line
```shell
php artisan pbuilder:install
```
In case of Artisan commands failure, use the alternative installation command:
```
php artisan pbuilder:altinstall
```
In case of links failure (if "pbstorage" links show error), navigate to "public folder", manually delete the link, and create a new one with the following command:
```
ln -s ../vendor/anibalealvarezs/projectbuilder-package/src/assets pbstorage
```

#### 3. Add full permissions to Jetstream API by default in ```app/Providers/JetstreamServiceProvider.php``` and remove the default permissions since they will be managed by Spatie's Permissions
```php
protected function configurePermissions()
{
    Jetstream::defaultApiTokenPermissions(['create', 'read', 'update', 'delete']);
}
```

#### 4. Comment/remove the default routes in ```/routes/web.php```
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
