# Project Builder

***

## Requirements

#### 0. Prepare your .env file
Submit database connection data, site URL and project name. Then, install debugbar package manually
```shell
composer require barryvdh/laravel-debugbar --dev
```

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

#### 2. Add "pbstorage" link to "config/filesystems.php"
```php
'links' => [
        public_path('pbstorage') => app_path('vendor/anibalealvarezs/projectbuilder-package/src/assets'),
    ],
```

***

## Installation

#### 3. Require the package & install it
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

#### 4. Add full permissions to Jetstream API by default in ```app/Providers/JetstreamServiceProvider.php``` and remove the default permissions since they will be managed by Spatie's Permissions
```php
protected function configurePermissions()
{
    Jetstream::defaultApiTokenPermissions(['create', 'read', 'update', 'delete']);
}
```

#### 5. Comment/remove the default routes in ```/routes/web.php```
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

#### 6. Add resources to ```/webpack.mix.js```
```javascript
mix.js('node_modules/sweetalert2/dist/sweetalert2.js', 'public/js').
    js('node_modules/sortablejs/Sortable.js', 'public/js').
    js('resources/js/app.js', 'public/js').vue()
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .webpackConfig(require('./webpack.config'));
```

#### 7. Install new resources as dependencies
```shell
npm i sweetalert2
npm install @tailwindcss/forms
npm install sortablejs --save
```

#### 8. Recompile app.js
```shell
npm run prod
```
