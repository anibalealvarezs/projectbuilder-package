<?php

namespace Anibalealvarezs\Projectbuilder\Commands;

use Anibalealvarezs\Projectbuilder\Traits\PbInstallTrait;
use Illuminate\Console\Command;

class PbAltInstallCommand extends Command
{
    use PbInstallTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbuilder:altinstall
                            {--all : All tasks will be performed}
                            {--update : pachage will be updated before installation}
                            {--inertia : Includes Jetstream and Inertia installation}
                            {--publish : Resources will be published to the application}
                            {--migrate : Migrations will be run}
                            {--seed : Tables will be seeded}
                            {--config : Application wil be configured}
                            {--link : Links will be created}
                            {--npm : npm resources will be required}
                            {--compile : npm will be run}
                            {--refresh : database will be reset on migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Builder alternative installation';

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function installInertia(): bool
    {
        try {
            echo "---- Requiring Jetstream...\n";
            if (!shell_exec("composer require laravel/jetstream")) {
                echo "------ [[ ERROR: composer command through shell_exec failed ]]\n";
                return false;
            }
            echo "---- Installing Jetstream & Inertia...\n";
            if (!shell_exec("php artisan jetstream:install inertia --teams --pest")) {
                echo "------ [[ ERROR: Jetstream installation failed failed ]]\n";
                return false;
            }
            echo "---- Installing providers...\n";
            if (!$this->installProviders()) {
                echo "------ [[ ERROR: proviers couldn't be installed ]]\n";
                return false;
            }
        } catch (Exception $e) {
            echo "------ [[ ERROR: ".$e->getMessage()." ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function publishResources(): bool
    {
        try {
            echo "------ Publishing Sanctum's resources...\n";
            if (!shell_exec("php artisan vendor:publish --provider=\"Laravel\Sanctum\SanctumServiceProvider\"")) {
                echo "-------- [[ ERROR: Sanctum's resources could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Spatie's resources...\n";
            if (!shell_exec("php artisan vendor:publish --provider=\"Spatie\Permission\PermissionServiceProvider\"")) {
                echo "-------- [[ ERROR: Spatie's resources could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Debugbar's resources...\n";
            if (!shell_exec("php artisan vendor:publish --provider=\"Barryvdh\Debugbar\ServiceProvider\"")) {
                echo "-------- [[ ERROR: Debugbar's resources could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Project Builder's stubs... \n";
            if (!shell_exec("php artisan vendor:publish --provider=\"Anibalealvarezs\Projectbuilder\Providers\PbMigrationServiceProvider\" --tag=\"migrations\"")) {
                echo "-------- [[ ERROR: Project Builder's stubs could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Project Builder's schema dump... \n";
            if (!shell_exec("php artisan vendor:publish --provider=\"Anibalealvarezs\Projectbuilder\Providers\PbMigrationServiceProvider\" --tag=\"schema\"")) {
                echo "-------- [[ ERROR: Project Builder's stubs could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Project Builder's views and config files... \n";
            if (!shell_exec("php artisan vendor:publish --provider=\"Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider\" --tag=\"Pb-views\" --force")) {
                echo "-------- [[ ERROR: Project Builder's views could not be published ]]\n";
                return false;
            }
        } catch (Exception $e) {
            echo "------ [[ ERROR: ".$e->getMessage()." ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function migrateAndSeed(): bool
    {
        try {
            if ($this->option('migrate') || $this->option('all') || (str_starts_with($this->signature,  'pbuilder:altupdate'))) {
                echo "------ Clearing cache... \n";
                if (!shell_exec("php artisan cache:clear")) {
                    echo "-------- [[ ERROR: Cache could not be cleared ]]\n";
                    return false;
                }
                if ($this->option('refresh')) {
                    echo "------ Resetting database... \n";
                    if (!shell_exec("php artisan migrate:refresh")) {
                        echo "-------- [[ ERROR: Database reset failed ]]\n";
                        return false;
                    }
                } else {
                    echo "------ Migrating... \n";
                    if (!shell_exec("php artisan migrate")) {
                        echo "-------- [[ ERROR: Migration failed ]]\n";
                        return false;
                    }
                }
            }
            if ($this->option('seed') || $this->option('all')) {
                echo "------ Seeding... \n";
                if (!shell_exec("php artisan db:seed --class=\"\\Anibalealvarezs\\Projectbuilder\\Database\\Seeders\\PbMainSeeder\"")) {
                    echo "-------- [[ ERROR: Tables could not be seeded ]]\n";
                    return false;
                }
            }
        } catch (Exception $e) {
            echo "-------- [[ ERROR: ".$e->getMessage()." ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Scope a query to only include popular users.
     *
     * @return bool
     */
    public function createLinks(): bool
    {
        echo "------ Adding storage path...\n";
        if (!$this->addStoragePath()) {
            return false;
        }
        echo "------ Creating links...\n";
        if (!shell_exec("php artisan storage:link")) {
            echo "------ [[ WARNING: Links could not be created ]]\n";
        }
        return true;
    }
}
