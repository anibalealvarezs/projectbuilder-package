# Project Builder

***

## Requierements

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

#### 2. Add "pbstorage" link to "config/app/filesystems.php"
```php
'links' => [
        public_path('pbstorage') => app_path('vendor/anibalealvarezs/projectbuilder-package/src/assets'),
    ],
```

#### 3. If ***NOT*** installed, let's requiere jetstream and install inertia
```shell
composer require laravel/jetstream
php artisan jetstream:install inertia --teams
```

***

## Installation

#### 3. Require the package
```shell
composer require anibalealvarezs/projectbuilder-package --no-cache
```

#### 4. Publish Spatie's and ProjectBuilder's migrations
Probably, after publishing, you'll have to change the file "date name" for it to be run before changes to roles table in other migration files
```shell
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbMigrationServiceProvider" --tag=migrations
```

#### 5. Clear config cache
```shell
php artisan config:clear
```

#### 6. Migrate and seed the DB
```shell
php artisan migrate
php artisan db:seed --class=\\Anibalealvarezs\\Projectbuilder\\Database\\Seeders\\PbMainSeeder
```
in case of failing when turning columns to JSON, ***check manually the tables and transform them to json object and then re-seed***

[//]: <> (#### 7. OPTIONALLY, seed the DB step by step)
[//]: <> (These are the default seeders in case you want to run them manually)
[//]: <> (```shell)
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbCitiesSeeder")
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbConfigSeeder")
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbLoggerSeeder")
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbNavigationSeeder")
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbSpatieSeeder")

[//]: <> (The following are already included in previous seeds:)
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbCountriesSeeder")
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbLanguagesSeeder")
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbUsersSeeder")
[//]: <> (php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbTeamSeeder")
[//]: <> (```)

#### 7. Publish Vue components and libraries
Publish all necessary files
```
php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-views" --force
```
[//]: <> (or publish them one by one)
[//]: <> (```shell)
[//]: <> (php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-components" --force)
[//]: <> (php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-js" --force)
[//]: <> (php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-css" --force)
[//]: <> (php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-blade" --force)
[//]: <> (php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-core" --force)
[//]: <> (php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-fonts" --force)
[//]: <> (php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-img" --force)
[//]: <> (```)
Ignore any alert from any seeder. Not all folders are used with every package.

#### 9. Create default "storage" and "pbstorage" links
```shell
php artisan storage:link
```
if "pbstorage" links show error, navigate to "public folder", manually delete the link, and create a new one with the following command:
```
ln -s ../vendor/anibalealvarezs/projectbuilder-package/src/assets pbstorage
```

#### 10. Comment/remove the default "dashboard" route in /routes/web.php
```php
/*
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');
*/
```
Optionally, add a default view for root path
```php
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
```

#### 11. Add resources to /webpack.mix.js
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

#### 12. Install new resources as dependencies
```shell
npm i sweetalert2
npm install @tailwindcss/forms
npm install sortablejs --save
```

[//]: <> (#### 13. Require Tailwind Plugins in tailwind.config.js)
[//]: <> (```javascript)
[//]: <> (require\('@tailwindcss/forms'\))
[//]: <> (```)

#### 13. Add alias for public folder in webpack.config.js
```
Pub: path.resolve('public'),
```

#### 14. Recompile app.js
[//]: <> (For production:)
```shell
npm run prod
```
[//]: <> (For developing:)
[//]: <> (```shell)
[//]: <> (npm run watch)
[//]: <> (```)

[//]: <> (### Useful Commands:)

[//]: <> (```shell)
[//]: <> (php artisan cache:clear)
[//]: <> (php artisan route:clear)
[//]: <> (php artisan config:clear)
[//]: <> (php artisan view:clear)
[//]: <> (php artisan view:cache)

[//]: <> (php artisan clear-compiled)
[//]: <> (composer dump-autoload)
[//]: <> (php artisan optimize)
[//]: <> (```)

[//]: <> (### Developing Suite)

[//]: <> (Clone the <a href="https://github.com/anibalealvarezs/builderdev">BuilderDev repository</a> in order to continue this package developing)
