<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Auth;
use Illuminate\Support\Str;

trait PbInstallTrait
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        try {
            if (!env('DB_PASSWORD')) {
                echo "[[ Please prepare your ENV file with the DB data before installing the package ]]\n";
                return false;
            }
            if (!is_callable('shell_exec') || (false !== stripos(ini_get('disable_functions'), 'shell_exec'))) {
                echo "[[ Please enable 'shell_exec' PHP function in order to fully install this package ]]\n";
                return false;
            }
            echo "[[ Process start ]]\n";
            if (!class_exists(\Inertia\Inertia::class)) {
                // Ask for confirmation for installing Inertia
                if ($this->confirm("'Jetstream/Inertia with Teams and Pest' is required. Do you want Project Builder to install it for you?")) {
                    // Inertia
                    echo "-- [[ Installing pre-requirements ]]\n";
                    if (!$this->installInertia()) {
                        return false;
                    }
                } else {
                    echo "[[ Please install 'Jetstream/Inertia with Teams and Pest' before installing Project Builder ]]\n";
                    return false;
                }
            }
            // Project Builder...
            echo "-- [[ Installing Project Builder ]]\n";
            if (!$this->installProjectBuilder()) {
                return false;
            }
            // Config files...
            if (!$this->modifyFiles()) {
                return false;
            }
            if ($this->option('compile') || $this->option('all') || (str_starts_with($this->signature,  'pbuilder:update')) || (str_starts_with($this->signature,  'pbuilder:altupdate'))) {
                // Compilation...
                if (!$this->compileAssets()) {
                    echo "---- [[ ERROR: Assets couldn't be compiled ]]\n";
                    return false;
                }
            }
            echo "[[ Process finished ]]\n";
        } catch (Exception $e) {
            echo "-- [[ ERROR: ".$e->getMessage()." ]]\n";
        }
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function installProjectBuilder(): bool
    {
        try {
            if ($this->option('publish') || $this->option('all') || (str_starts_with($this->signature,  'pbuilder:update')) || (str_starts_with($this->signature,  'pbuilder:altupdate'))) {
                echo "---- Installing providers...\n";
                if (!$this->installProviders()) {
                    echo "------ [[ ERROR: proviers couldn't be installed ]]\n";
                    return false;
                }
            }
            if ($this->option('update') || $this->option('all')) {
                echo "---- Looking for Project Builder's package last version...\n";
                if (!$this->requirePackage()) {
                    echo "------ [[ ERROR: composer could not require Project Builder's package ]]\n";
                    return false;
                }
            }
            if ($this->option('publish') || $this->option('all') || (str_starts_with($this->signature,  'pbuilder:update')) || (str_starts_with($this->signature,  'pbuilder:altupdate'))) {
                echo "---- Publishing Resources...\n";
                if (!$this->publishResources()) {
                    return false;
                }
            }
            if ($this->option('migrate') || $this->option('seed') || $this->option('all') || (str_starts_with($this->signature,  'pbuilder:update')) || (str_starts_with($this->signature,  'pbuilder:altupdate'))) {
                if ($this->confirm("'Do you want to remove previous migration files?")) {
                    echo "------ Removing previous migration files\n";
                    if (!$this->removePreviousMigrations()) {
                        echo "-------- [[ ERROR: Error removing migrations files ]]\n";
                        return false;
                    }
                }
                echo "---- Configurating database...\n";
                if (!$this->migrateAndSeed()) {
                    return false;
                }
            }
            if ($this->option('link') || $this->option('all')) {
                echo "---- Creating links...\n";
                if (!$this->createLinks()) {
                    echo "---- [[ Error creating links ]]\n";
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function requirePackage(): bool
    {
        echo "------ [[ Requiring package ]]\n";
        return shell_exec("composer require anibalealvarezs/projectbuilder-package --no-cache");
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function compileAssets(): bool
    {
        echo "-- [[ Compiling assets ]]\n";
        return shell_exec("npm run prod");
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function checkMysql(): bool
    {
        echo "---- Checking MySQL...\n";
        return shell_exec('mysql --version');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function installProviders(): bool
    {
        // Default providers...
        // -- Fortify
        // -- Jetstream
        // -- Project Builder
        echo "---- Installing service providers...\n";
        if (!$this->putProviders()) {
            return false;
        }
        echo "---- Ignoring jetstream and fortify auto-discovery...\n";
        if (!$this->ignoreJetstreamAutodiscovery()) {
            echo "------ [[ ERROR: composer.json couldn't be editted ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function installNpmResources(): bool
    {
        echo "---- Installing Sweetalert2.js...\n";
        if (!shell_exec("npm i sweetalert2")) {
            echo "------ [[ ERROR: Sweetalert2.js could't be installed ]]\n";
            return false;
        }
        echo "---- Installing Sortable.js...\n";
        if (!shell_exec("npm install sortablejs --save")) {
            echo "------ [[ ERROR: Sortable.js could't be installed ]]\n";
            return false;
        }
        echo "---- Installing Flag Icons...\n";
        if (!shell_exec("npm install --dev flag-icons")) {
            echo "------ [[ ERROR: Flag Icons could't be installed ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function modifyFiles(): bool
    {
        if ($this->option('config') || $this->option('all')) {
            // Enable additional sanctum's middleware...
            echo "---- Enabling additional Sanctum's middleware...\n";
            if (!$this->enableAdditionalSanctumMiddleware()) {
                return false;
            }
            // Enable full jestream features...
            echo "---- Enabling full Jetsream's features...\n";
            if (!$this->enableFullJetstreamFeatures()) {
                return false;
            }
            // Add 'Pub' path to webpack...
            echo "---- Enabling 'Pub' path to webpack...\n";
            if (!$this->addPubPath()) {
                return false;
            }
            // Replace feature flag globals for esm-bundler build of Vue...
            echo "---- Replacing feature flag globals for esm-bundler build of Vue...\n";
            if (!$this->replaceFeatureFlagGlobals()) {
                return false;
            }
            // Enable source map for debug...
            echo "---- Enabling source map...\n";
            if (!$this->enableSourceMap()) {
                return false;
            }
        }
        if ($this->option('npm') || $this->option('all') || (str_starts_with($this->signature,  'pbuilder:update')) || (str_starts_with($this->signature,  'pbuilder:altupdate'))) {
            // Install npm resources...
            echo "---- Installing npm resources...\n";
            if (!$this->installNpmResources()) {
                return false;
            }
            // Add resources to Mix...
            echo "---- Adding resources to Mix...\n";
            if (!$this->addResourcesToMix()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param $after
     * @return bool
     */
    protected function putProviders(): bool
    {
        $appConfig = file_get_contents(config_path('app.php'));
        $newAppConfig = $appConfig;
        if (! Str::contains($newAppConfig, 'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamAppServiceProvider::class')) {
            if (Str::contains($newAppConfig, 'App\\Providers\\JetstreamServiceProvider::class')) {
                $newAppConfig = str_replace(
                    'App\\Providers\\JetstreamServiceProvider::class,',
                    'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamAppServiceProvider::class,',
                    $newAppConfig
                );
            } else {
                $newAppConfig = str_replace(
                    'App\\Providers\\FortifyServiceProvider::class,',
                    'App\\Providers\\FortifyServiceProvider::class,'.PHP_EOL.'        Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamAppServiceProvider::class,',
                    $newAppConfig
                );
            }
        }
        if (! Str::contains($newAppConfig, 'Anibalealvarezs\\Projectbuilder\\Providers\\PbFortifyServiceProvider::class')) {
            if (Str::contains($newAppConfig, 'Laravel\\Fortify\\FortifyServiceProvider::class')) {
                $newAppConfig = str_replace(
                    'Laravel\\Fortify\\FortifyServiceProvider::class,',
                    'Anibalealvarezs\\Projectbuilder\\Providers\\PbFortifyServiceProvider::class,',
                    $newAppConfig
                );
            } else {
                $newAppConfig = str_replace(
                     'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamAppServiceProvider::class,',
                     'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamAppServiceProvider::class,'.PHP_EOL.'        Anibalealvarezs\\Projectbuilder\\Providers\\PbFortifyServiceProvider::class,',
                    $newAppConfig
                 );
            }
        }
        if (! Str::contains($newAppConfig, 'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class')) {
            if (Str::contains($newAppConfig, 'Laravel\\Jetstream\\JetstreamServiceProvider::class')) {
                $newAppConfig = str_replace(
                    'Laravel\\Jetstream\\JetstreamServiceProvider::class,',
                    'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class,',
                    $newAppConfig
                );
            } else {
                $newAppConfig = str_replace(
                    'Anibalealvarezs\\Projectbuilder\\Providers\\PbFortifyServiceProvider::class,',
                    'Anibalealvarezs\\Projectbuilder\\Providers\\PbFortifyServiceProvider::class,'.PHP_EOL.'        Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class,',
                    $newAppConfig
                );
            }
        }
        if (! Str::contains($newAppConfig, 'Anibalealvarezs\\Projectbuilder\\Providers\\PbRouteServiceProvider::class')) {
            $newAppConfig = str_replace(
                'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class,',
                'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class,'.PHP_EOL.'        Anibalealvarezs\\Projectbuilder\\Providers\\PbRouteServiceProvider::class,',
                $newAppConfig
            );
        }
        if ($appConfig !== $newAppConfig) {
            if (!file_put_contents(config_path('app.php'), $newAppConfig)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function enableAdditionalSanctumMiddleware(): bool
    {
        if (! Str::contains($appHttpKernel = file_get_contents(base_path('/app/Http/Kernel.php')), '\\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class,')) {
            if (!file_put_contents(base_path('/app/Http/Kernel.php'), str_replace(
                '\'throttle:api\',',
                '\'throttle:api\','.PHP_EOL.'            \\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class,',
                $appHttpKernel
            ))) {
                return false;
            }
        } elseif (Str::contains($appHttpKernel = file_get_contents(base_path('/app/Http/Kernel.php')), '// \\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class,')) {
            if (!file_put_contents(base_path('/app/Http/Kernel.php'), str_replace(
                '// \\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class,',
                '\\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class,',
                $appHttpKernel
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function enableFullJetstreamFeatures(): bool
    {
        if (Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::termsAndPrivacyPolicy(),')) {
            if (!file_put_contents(config_path('jetstream.php'), str_replace(
                '// Features::termsAndPrivacyPolicy(),',
                'Features::termsAndPrivacyPolicy(),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::profilePhotos(),')) {
            if (!file_put_contents(config_path('jetstream.php'), str_replace(
                '// Features::profilePhotos(),',
                'Features::profilePhotos(),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::api(),')) {
            if (!file_put_contents(config_path('jetstream.php'), str_replace(
                '// Features::api(),',
                'Features::api(),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::teams([\'invitations\' => true]),')) {
            if (!file_put_contents(config_path('jetstream.php'), str_replace(
                '// Features::teams([\'invitations\' => true]),',
                'Features::teams([\'invitations\' => true]),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::accountDeletion(),')) {
            if (!file_put_contents(config_path('jetstream.php'), str_replace(
                '// Features::accountDeletion(),',
                'Features::accountDeletion(),',
                $appJetstream
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function addPubPath(): bool
    {
        if (! Str::contains($webpackConfig = file_get_contents(base_path('/webpack.config.js')), 'Pub: path.resolve(\'public\'),')) {
            if (!file_put_contents(base_path('/webpack.config.js'), str_replace(
                '\'@\': path.resolve(\'resources/js\'),',
                '\'@\': path.resolve(\'resources/js\'),'.PHP_EOL.'            Pub: path.resolve(\'public\'),',
                $webpackConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function removePreviousMigrations(): bool
    {
        if (!shell_exec("rm ".database_path('migrations' . DIRECTORY_SEPARATOR)."*")) {
            return false;
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function replaceFeatureFlagGlobals(): bool
    {
        if (! Str::contains($webpackConfig = file_get_contents(base_path('/webpack.config.js')), 'const webpack = require(\'webpack\');')) {
            if (!file_put_contents(base_path('/webpack.config.js'), str_replace(
                'const path = require(\'path\');',
                'const path = require(\'path\');'.PHP_EOL.'const webpack = require(\'webpack\');',
                $webpackConfig
            ))) {
                return false;
            }
        }
        if (! Str::contains($webpackConfig = file_get_contents(base_path('/webpack.config.js')), '__VUE_OPTIONS_API__: JSON.stringify(true),')) {
            if (!file_put_contents(base_path('/webpack.config.js'), str_replace(
                '};',
                '    plugins: ['.PHP_EOL.'        new webpack.DefinePlugin({'.PHP_EOL.'            __VUE_OPTIONS_API__: JSON.stringify(true),'.PHP_EOL.'            __VUE_PROD_DEVTOOLS__: JSON.stringify(false),'.PHP_EOL.'        }),'.PHP_EOL.'    ],'.PHP_EOL.'};',
                $webpackConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function enableSourceMap(): bool
    {
        if (! Str::contains($webpackConfig = file_get_contents(base_path('/webpack.config.js')), 'devtool: "source-map",')) {
            if (!file_put_contents(base_path('/webpack.config.js'), str_replace(
                '};',
                '    devtool: "source-map",'.PHP_EOL.'};',
                $webpackConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function addStoragePath(): bool
    {
        if (! Str::contains($webpackConfig = file_get_contents(config_path('filesystems.php')), 'pbstorage')) {
            if (!file_put_contents(config_path('filesystems.php'), str_replace(
                'public_path(\'storage\') => storage_path(\'app/public\'),',
                'public_path(\'storage\') => storage_path(\'app/public\'),'.PHP_EOL.'        public_path(\'pbstorage\') => base_path(\'vendor/anibalealvarezs/projectbuilder-package/src/assets\'),',
                $webpackConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function ignoreJetstreamAutodiscovery(): bool
    {
        if (Str::contains($webpackConfig = file_get_contents(base_path('/composer.json')), '"dont-discover": []')) {
            if (!file_put_contents(base_path('/composer.json'), str_replace(
                '"dont-discover": []',
                '"dont-discover": ['.PHP_EOL.'                "laravel/jetstream",'.PHP_EOL.'                "laravel/fortify"'.PHP_EOL.'            ]',
                $webpackConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    protected function addResourcesToMix(): bool
    {
        if (! Str::contains($webpackMix = file_get_contents(base_path('/webpack.mix.js')), 'sweetalert2.js')) {
            if (!file_put_contents(base_path('/webpack.mix.js'), str_replace(
                'js(\'resources/js/app.js\', \'public/js\')',
                'js(\'node_modules/sweetalert2/dist/sweetalert2.js\', \'public/js\').'.PHP_EOL.'    js(\'resources/js/app.js\', \'public/js\')',
                $webpackMix
            ))) {
                return false;
            }
        }
        if (! Str::contains($webpackMix = file_get_contents(base_path('/webpack.mix.js')), 'Sortable.js')) {
            if (!file_put_contents(base_path('/webpack.mix.js'), str_replace(
                'js(\'resources/js/app.js\', \'public/js\')',
                'js(\'node_modules/sortablejs/Sortable.js\', \'public/js\').'.PHP_EOL.'    js(\'resources/js/app.js\', \'public/js\')',
                $webpackMix
            ))) {
                return false;
            }
        }
        if (! Str::contains($webpackMix = file_get_contents(base_path('/webpack.mix.js')), 'flag-icons.css')) {
            if (!file_put_contents(base_path('/webpack.mix.js'), str_replace(
                '.webpackConfig(require(\'./webpack.config\'));',
                '.postCss(\'node_modules/flag-icons/css/flag-icons.css\', \'public/css\')'.PHP_EOL.'    .webpackConfig(require(\'./webpack.config\'));',
                $webpackMix
            ))) {
                return false;
            }
        }
        return true;
    }
}
