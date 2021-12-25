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
            if ($this->option('inertia')) {
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
            // Default providers...
            // -- Fortify
            // -- Jetstream
            echo "-- [[ Installing Fortify and Jetstream service providers ]]\n";
            if (!$this->installDefaultProvidersAfter('JetstreamServiceProvider')) {
                return false;
            }
            // Projectbuilder's providers...
            echo "-- [[ Installing custom Jetstream service providers ]]\n";
            if (!$this->installAdditionalProviders('PbJetstreamServiceProvider')) {
                return false;
            }
            echo "-- [[ Installing Builder's route service provider ]]\n";
            if (!$this->installAdditionalProviders('PbRouteServiceProvider')) {
                return false;
            }
            // Enable additional sanctum's middleware...
            echo "-- [[ Enabling additional Sanctum's middleware ]]\n";
            if (!$this->enableAdditionalSanctumMiddleware()) {
                return false;
            }
            // Enable full jestream features...
            echo "-- [[ Enabling full Jetsream's features ]]\n";
            if (!$this->enableFullJetstreamFeatures()) {
                return false;
            }
            // Add 'Pub' path to webpack...
            echo "-- [[ Enabling 'Pub' path to webpack ]]\n";
            if (!$this->addPubPath()) {
                return false;
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
            echo "---- Looking for Project Builder's package updates...\n";
            if (!$this->requirePackage()) {
                return false;
            }
            echo "---- Publishing Resources...\n";
            if (!$this->publishResources()) {
                return false;
            }
            echo "---- Database configuiration...\n";
            if (!$this->migrateAndSeed()) {
                return false;
            }
            echo "---- Creating links...\n";
            if (!$this->createLinks()) {
                if (\Anibalealvarezs\Projectbuilder\Models\PbLogger::create(['severity' => 2, 'message' => 'Links creation failed', 'object_type' => null])) {
                    echo "---- [[ Confirmation log entry added ]]\n";
                } else {
                    echo "---- [[ Error adding confirmation log entry ]]\n";
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
            if (!file_put_contents(config_path('/app/Http/Kernel.php'), str_replace(
                '\\Illuminate\\Routing\\Middleware\\SubstituteBindings::class,',
                '\\Illuminate\\Routing\\Middleware\\SubstituteBindings::class,'.PHP_EOL.'        \\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class,',
                $appHttpKernel
            ))) {
                return false;
            }
        } elseif (! Str::contains($appHttpKernel = file_get_contents(base_path('/app/Http/Kernel.php')), '// \\Laravel\\Sanctum\\Http\\Middleware\\EnsureFrontendRequestsAreStateful::class,')) {
            if (!file_put_contents(config_path('/app/Http/Kernel.php'), str_replace(
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
        if (! Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::termsAndPrivacyPolicy(),')) {
            if (!file_put_contents(config_path('/app/Http/Kernel.php'), str_replace(
                '// Features::termsAndPrivacyPolicy(),',
                'Features::termsAndPrivacyPolicy(),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (! Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::profilePhotos(),')) {
            if (!file_put_contents(config_path('/app/Http/Kernel.php'), str_replace(
                '// Features::profilePhotos(),',
                'Features::profilePhotos(),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (! Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::api(),')) {
            if (!file_put_contents(config_path('/app/Http/Kernel.php'), str_replace(
                '// Features::api(),',
                'Features::api(),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (! Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::teams([\'invitations\' => true]),')) {
            if (!file_put_contents(config_path('/app/Http/Kernel.php'), str_replace(
                '// Features::teams([\'invitations\' => true]),',
                'Features::teams([\'invitations\' => true]),',
                $appJetstream
            ))) {
                return false;
            }
        }
        if (! Str::contains($appJetstream = file_get_contents(config_path('jetstream.php')), '// Features::accountDeletion(),')) {
            if (!file_put_contents(config_path('/app/Http/Kernel.php'), str_replace(
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
        if (! Str::contains($webpackConfig = file_get_contents(base_path('webpack.config.js')), 'Pub: path.resolve(\'public\'),')) {
            if (!file_put_contents(config_path('webpack.config.js'), str_replace(
                '\'@\': path.resolve(\'resources/js\'),',
                '\'@\': path.resolve(\'resources/js\'),'.PHP_EOL.'        Pub: path.resolve(\'public\'),',
                $webpackConfig
            ))) {
                return false;
            }
        }
        return true;
    }
}
