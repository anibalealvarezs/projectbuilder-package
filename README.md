# Project Builder

### Instructions for installation:

#### 1. Add the following lines to composer.json:
```
"repositories":[
    {"type": "composer", "url": "https://repo.packagist.com/anibal-alvarez/"},
    {"packagist.org": false}
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

#### 6. Migrate the DB
```
php artisan migrate
```
or, in case of migration failure (***NOT FOR RUNNING PROJECTS SINCE DB WILL BE WIPED OUT***),
```
php artisan migrate:refresh --seed
```

#### 7. Publish Project Builder's Seeders
(***ONLY IF INSTALLING FOR THE VERY FIRST TIME SINCE SEEDS COULD GET DUPLICATED***)
```
php artisan db:seed
```
If not, install these seeds manually
```
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbSpatieSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbUsersSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbTeamSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbLoggerSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbConfigSeeder"
```

#### 8. Add "pbstorage" link to "app/filesystems.php"
```
'links' => [
        public_path('pbstorage') => app_path('vendor/anibalealvarezs/projectbuilder-package/src/assets'),
    ],
```

#### 9. Create default "storage" and "pbstorage" links
```
php artisan storage:link
```
if "pbstorage" links show error, navigate to "public folder", manually delete the link, and create a new one with the following command:
```
ln -s ../vendor/anibalealvarezs/projectbuilder-package/src/assets pbstorage
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
