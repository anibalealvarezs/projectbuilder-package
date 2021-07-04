# Project Builder

### Instructions for installation:

#### 1. Add the following lines to composer.json:
```
"repositories": [
    {
        "type": "composer",
        "url": "https://satis.anibalalvarez.com"
    }
],
```

#### 2. If ***NOT*** installed, let's requiere jetstream and install inertia
```
composer require laravel/jetstream
php artisan jetstream:install inertia --teams
```

#### 3. Require the package
```
composer require anibalealvarezs/projectbuilder-package --no-cache
```

#### 4. Publish Spatie's Migration
```
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

#### 5. Clear config cache
```
php artisan config:clear
```

#### 6. Migrate and seed the DB
```
php artisan migrate
php artisan db:seed --class=\\Anibalealvarezs\\Projectbuilder\\Database\\Seeders\\PbMainSeeder
```
in case of failing when turning columns to JSON, ***check manually the tables and transform to json object, and then re-seed***

#### 7. OPTIONALLY, seed the DB step by step
These are the default seeders in case you want to run them manually
```
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbCitiesSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbConfigSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbLoggerSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbNavigationSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbSpatieSeeder"

The following are already included in previous seeds:
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbCountriesSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbLanguagesSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbUsersSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbTeamSeeder"
```

### 8. Publish Vue components and libraries
Publish all necessary files
```
php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-views" --force
```
or publish them one by one
```
php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-components" --force
php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-js" --force
php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-css" --force
php artisan vendor:publish --provider="Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider" --tag="builder-core" --force
```

#### 9. Add "pbstorage" link to "app/filesystems.php"
```
'links' => [
        public_path('pbstorage') => app_path('vendor/anibalealvarezs/projectbuilder-package/src/assets'),
    ],
```

#### 10. Create default "storage" and "pbstorage" links
```
php artisan storage:link
```
if "pbstorage" links show error, navigate to "public folder", manually delete the link, and create a new one with the following command:
```
ln -s ../vendor/anibalealvarezs/projectbuilder-package/src/assets pbstorage
```

### 11. Comment/remove the default "dashboard" route in /routes/web.php
```
/* Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard'); */
```

### 12. Add resources to /webpack.mix.js
```
mix.js('node_modules/sweetalert2/dist/sweetalert2.js', 'public/js');
```

### 13. Install new resources as dependencies
```
npm i sweetalert2
npm install @tailwindcss/forms
```

### 14. Require Tailwind Plugins in tailwind.config.js
```
require('@tailwindcss/forms')
```

### 15. Add alias for public folder in webpack.config.js
```
Pub: path.resolve('public'),
```

### 16. Recompile app.js
For production:
```
npm run prod
```
For developing:
```
npm run watch
```

### Useful Commands:

```
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan view:cache

php artisan clear-compiled
composer dump-autoload
php artisan optimize
```

### Developing Suite

Clone the <a href="https://github.com/anibalealvarezs/builderdev">BuilderDev repository</a> in order to continue this package developing
