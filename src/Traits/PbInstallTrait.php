<?php

namespace Anibalealvarezs\Projectbuilder\Traits;

use Auth;
use Illuminate\Support\Str;

trait PbInstallTrait
{
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

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
            // Inertia...
            if ($this->option('inertia') || $this->option('all')) {
                echo "-- [[ Installing pre-requirements ]]\n";
                if (!$this->installInertia()) {
                    return false;
                }
            }
            // Project Builder...
            echo "-- [[ Installing Project Builder ]]\n";
            if (!$this->installProjectBuilder()) {
                return false;
            }
            // Logging results...
            if (\Anibalealvarezs\Projectbuilder\Models\PbLogger::updateOrCreate(['message' => 'App Created'], ['object_type' => null])) {
                echo "---- [[ Confirmation log entry added ]]\n";
            } else {
                echo "---- [[ Error adding confirmation log entry ]]\n";
            }
            // Config files...
            if (!$this->modifyFiles()) {
                return false;
            }
            if ($this->option('compile') || $this->option('all')) {
                // Compilation...
                echo "-- [[ Compiling assets ]]\n";
                if (!$this->compileAssets()) {
                    return false;
                }
            }
            echo "[[ Process finished ]]\n";
        } catch (Exception $e) {
            echo "-- [[ ERROR: ".$e->getMessage()." ]]\n";
        }
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function installProjectBuilder()
    {
        try {
            echo "---- Looking for Project Builder's package last version...\n";
            if (!$this->requirePackage()) {
                return false;
            }
            if ($this->option('publish') || $this->option('all')) {
                echo "---- Publishing Resources...\n";
                if (!$this->publishResources()) {
                    return false;
                }
            }
            if ($this->option('migrate') || $this->option('seed') || $this->option('all')) {
                echo "---- Database configuiration...\n";
                if (!$this->migrateAndSeed()) {
                    return false;
                }
            }
            if ($this->option('link') || $this->option('all')) {
                echo "---- Creating links...\n";
                if (!$this->createLinks()) {
                    if (\Anibalealvarezs\Projectbuilder\Models\PbLogger::create(['severity' => 2, 'message' => 'Links creation failed', 'object_type' => null])) {
                        echo "---- [[ Confirmation log entry added ]]\n";
                    } else {
                        echo "---- [[ Error adding confirmation log entry ]]\n";
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function requirePackage()
    {
        if (!shell_exec("composer require anibalealvarezs/projectbuilder-package --no-cache")) {
            echo "------ [[ ERROR: composer could not require Project Builder's package ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function compileAssets()
    {
        if (!shell_exec("npm run prod")) {
            echo "------ [[ ERROR: Assets couldn't be compiled ]]\n";
            return false;
        }
        return true;
    }

    public function installProviders() {
        // Default providers...
        // -- Fortify
        // -- Jetstream
        echo "---- Installing Fortify and Jetstream service providers...\n";
        if (!$this->installDefaultProvidersAfter('JetstreamServiceProvider')) {
            return false;
        }
        // Projectbuilder's providers...
        echo "---- Installing custom Jetstream service providers...\n";
        if (!$this->installAdditionalProviders('PbJetstreamServiceProvider')) {
            return false;
        }
        echo "---- Installing Builder's route service provider...\n";
        if (!$this->installAdditionalProviders('PbRouteServiceProvider')) {
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
     * Execute the console command.
     *
     * @return void
     */

    public function installNpmResources()
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
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function modifyFiles()
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
        }
        if ($this->option('npm') || $this->option('all')) {
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
     * Install the service provider in the application configuration file.
     *
     * @param  string  $after
     * @param  string  $name
     * @return void
     */
    protected function installAdditionalProviders($name)
    {
        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'Anibalealvarezs\\Projectbuilder\\Providers\\'.$name.'::class')) {
            if (!file_put_contents(config_path('app.php'), str_replace(
                'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class,',
                'Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class,'.PHP_EOL.'        Anibalealvarezs\\Projectbuilder\\Providers\\'.$name.'::class,',
                $appConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Install the service provider in the application configuration file.
     *
     * @param  string  $after
     * @param  string  $name
     * @return void
     */
    protected function installDefaultProvidersAfter($after)
    {
        if (! Str::contains($appConfig = file_get_contents(config_path('app.php')), 'Laravel\\Fortify\\FortifyServiceProvider::class')) {
            if (!file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\'.$after.'::class,',
                'App\\Providers\\'.$after.'::class,'.PHP_EOL.'        Laravel\\Fortify\\FortifyServiceProvider::class,'.PHP_EOL.'        Anibalealvarezs\\Projectbuilder\\Providers\\PbJetstreamServiceProvider::class,',
                $appConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Install the service provider in the application configuration file.
     *
     * @return void
     */
    protected function enableAdditionalSanctumMiddleware()
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
     * Install the service provider in the application configuration file.
     *
     * @return void
     */
    protected function enableFullJetstreamFeatures()
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
     * Install the service provider in the application configuration file.
     *
     * @return void
     */
    protected function addPubPath()
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
     * Install the service provider in the application configuration file.
     *
     * @return void
     */
    protected function addStoragePath()
    {
        if (! Str::contains($webpackConfig = file_get_contents(config_path('filesystems.php')), 'pbstorage')) {
            if (!file_put_contents(config_path('filesystems.php'), str_replace(
                'public_path(\'storage\') => storage_path(\'app/public\'),',
                'public_path(\'storage\') => storage_path(\'app/public\'),'.PHP_EOL.'        public_path(\'pbstorage\') => base_path(\'/vendor/anibalealvarezs/projectbuilder-package/src/assets\'),',
                $webpackConfig
            ))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Install the service provider in the application configuration file.
     *
     * @return void
     */
    protected function ignoreJetstreamAutodiscovery()
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
     * Install the service provider in the application configuration file.
     *
     * @return void
     */
    protected function addResourcesToMix()
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
        return true;
    }
}
