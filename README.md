# Project Builder

### Instructions for installation:

#### 1. Add the following lines to composer.json:
```
"repositories":[
    {
        "type": "package",
        "package": {
            "name": "anibalealvarezs/projectbuilder-package",
            "version": "1.0.10",
            "source": {
                "url": "git@github.com:anibalealvarezs/projectbuilder-package.git",
                "type": "git",
                "reference": "master",
                "ssh2": { "username": "git",
                    "privkey_file": "~/github",
                    "pubkey_file": "~/github.pub"
                }
            },
            "autoload": {
                "psr-4": {
                    "Anibalealvarezs\\Projectbuilder\\": "src"
                }
            },
            "extra": {
                "laravel": {
                    "providers": [
                        "Anibalealvarezs\\Projectbuilder\\Providers\\PbComposerServiceProvider",
                        "Anibalealvarezs\\Projectbuilder\\Providers\\PbControllerServiceProvider",
                        "Anibalealvarezs\\Projectbuilder\\Providers\\PbMiddlewareServiceProvider",
                        "Anibalealvarezs\\Projectbuilder\\Providers\\PbMigrationServiceProvider",
                        "Anibalealvarezs\\Projectbuilder\\Providers\\PbRouteServiceProvider",
                        "Anibalealvarezs\\Projectbuilder\\Providers\\PbViewServiceProvider",
                        "Anibalealvarezs\\Projectbuilder\\Providers\\PbConfigServiceProvider"
                    ]
                }
            }
        }
    }
],
```

#### 2. If not installed, let's install livewire
```
php artisan jetstream:install livewire
```

#### 3. Publish Spatie's Migration
```
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

#### 4. Clear config cache
```
php artisan config:clear
```

#### 5. Migrate the DB
```
php artisan migrate
```
or, in case of migration failure,
```
php artisan migrate:refresh --seeds
```

#### 6. Publish Project Builder's Seeders
```
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbSpatieSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\Database\Seeders\PbUsersSeeder"
```

#### 7. Add "pbstorage" link to "app/filesystems.php"
```
'links' => [
        public_path('pbstorage') => app_path('vendor/anibalealvarezs/projectbuilder-package/src/assets'),
    ],
```

#### 8. Create default "storage" and "pbstorage" links
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