# Project Builder

***

## Requirements

#### 0. Prepare your .env file
Submit database connection data, site URL and project name

#### 1. Add the following lines to composer.json:
```json
"repositories": [
    {
        "type": "composer",
        "url": "https://satis.anibalalvarez.com"
    }
],
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
In case of links failure (if "pbstorage" links show error), navigate to "public folder", manually delete the link, and create a new one with the following command:
```
ln -s ../vendor/anibalealvarezs/projectbuilder-package/src/assets pbstorage
```

#### 4. Add Sanctum's middleware to your project's kernel in ```/app/Http/Kernel.php```
```php
'api' => [
    ...
    \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
    ...
],
```

#### 5. Enable full Jetstream features ```/config/jetstream.php```
```php
'features' => [
    Features::termsAndPrivacyPolicy(),
    Features::profilePhotos(),
    Features::api(),
    Features::teams(['invitations' => true]),
    Features::accountDeletion(),
],
```

#### 6. Add full permissions to Jetstream API by default in ```app/Providers/JetstreamServiceProvider.php``` and remove the default permissions since they will be managed by Spatie's Permissions
```php
protected function configurePermissions()
{
    Jetstream::defaultApiTokenPermissions(['create', 'read', 'update', 'delete']);
}
```

#### 7. Comment/remove the default routes in ```/routes/web.php```
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

#### 8. Add resources to ```/webpack.mix.js```
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

#### 9. Install new resources as dependencies
```shell
npm i sweetalert2
npm install @tailwindcss/forms
npm install sortablejs --save
```

#### 10. Add alias for public folder in ```/webpack.config.js```
```
Pub: path.resolve('public'),
```

#### 11. Recompile app.js
```shell
npm run prod
```
