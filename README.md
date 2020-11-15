# Project Builder

### Instructions for installation:

1. Add the following lines to composer.json:

```
"repositories":[
    {
        "type": "package",
        "package": {
            "name": "anibalealvarezs/projectbuilder-package",
            "version": "1.0.8",
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
                        "Anibalealvarezs\\Projectbuilder\\PbServiceProvider"
                    ]
                }
            }
        }
    }
],
```

2. Publish Spatie's Migration

```
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

3. Clear config cache
```
php artisan config:clear
```

4. Migrate the DB
```
php artisan migrate:refresh --seeds
```

5. Publish Project Builder's Seeders
```
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\PbSpatieSeeder"
php artisan db:seed --class="Anibalealvarezs\Projectbuilder\PbUsersSeeder"
```