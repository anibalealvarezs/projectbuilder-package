<?php

namespace Anibalealvarezs\Projectbuilder\Commands;

use Anibalealvarezs\Projectbuilder\Traits\PbInstallTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PbInstallCommand extends Command
{
    use PbInstallTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbuilder:install {--inertia : Includes Jetstream and Inertia installation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Builder installation';

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function installInertia()
    {
        try {
            echo "---- Requiring Jetstream...\n";
            if (!shell_exec("composer require laravel/jetstream")) {
                echo "------ [[ ERROR: composer command through shell_exec failed ]]\n";
                return false;
            }
            echo "---- Installing providers...\n";
            if (!$this->installProviders()) {
                echo "------ [[ ERROR: proviers couldn't be installed ]]\n";
                return false;
            }
            echo "---- Installing Jetstream & Inertia...\n";
            if (!Artisan::call(
                'jetstream:install',
                [
                    'stack' => 'inertia',
                    '--teams' => 'default',
                    '--pest' => 'default'
                ]
            )) {
                echo "------ [[ ERROR: Jetstream installation failed failed ]]\n";
                return false;
            }
        } catch (Exception $e) {
            echo "------ [[ ERROR: ".$e->getMessage()." ]]\n";
            return false;
        }
        return true;
    }

    public function republishJetstreamResources()
    {
        echo "------ Reloading classes...\n";
        if (!shell_exec("composer dump-autoload")) {
            echo "------ [[ ERROR: Classes could not be reloaded ]]\n";
            return false;
        }
        echo "------ Republishing laravel resources...\n";
        if (!Artisan::call(
            'vendor:publish',
            [
                '--tag' => 'laravel-assets',
                '--ansi' => 'default',
                '--force' => 'default',
            ]
        )) {
            echo "-------- [[ ERROR: Laravel assets could not be published ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function publishResources()
    {
        try {
            echo "------ Publishing Sanctum's resources...\n";
            if (!Artisan::call(
                'vendor:publish',
                [
                    '--provider' => 'Laravel\Sanctum\SanctumServiceProvider'
                ]
            )) {
                echo "-------- [[ ERROR: Sanctum's resources could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Spatie's resources...\n";
            if (!Artisan::call(
                'vendor:publish',
                [
                    '--provider' => 'Spatie\Permission\PermissionServiceProvider'
                ]
            )) {
                echo "-------- [[ ERROR: Spatie's resources could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Debugbar's resources...\n";
            if (!Artisan::call(
                'vendor:publish',
                [
                    '--provider' => 'Barryvdh\Debugbar\ServiceProvider'
                ]
            )) {
                echo "-------- [[ ERROR: Debugbar's resources could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Project Builder's stubs... \n";
            if (!Artisan::call(
                'vendor:publish',
                [
                    '--provider' => 'Anibalealvarezs\Projectbuilder\Providers\PbMigrationServiceProvider',
                    '--tag' => 'migrations'
                ]
            )) {
                echo "-------- [[ ERROR: Project Builder's stubs could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Project Builder's views... \n";
            if (!Artisan::call(
                'vendor:publish',
                [
                    '--provider' => 'Anibalealvarezs\Projectbuilder\Providers\PbViewServiceProvider',
                    '--tag' => 'builder-views',
                    '--force' => 'default'
                ]
            )) {
                echo "-------- [[ ERROR: Project Builder's views could not be published ]]\n";
                return false;
            }
            echo "------ Publishing Project Builder's config file... \n";
            if (!Artisan::call(
                'vendor:publish',
                [
                    '--provider' => 'Anibalealvarezs\Projectbuilder\Providers\PbConfigServiceProvider',
                    '--tag' => 'config'
                ]
            )) {
                echo "-------- [[ ERROR: Project Builder's config file could not be published ]]\n";
                return false;
            }
        } catch (Exception $e) {
            echo "------ [[ ERROR: ".$e->getMessage()." ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function migrateAndSeed()
    {
        try {

            echo "------ Clearing cache... \n";
            if (!Artisan::call('cache:clear')) {
                echo "-------- [[ ERROR: Cache could not be cleared ]]\n";
                return false;
            }
            echo "------ Migrating... \n";
            if (!Artisan::call('migrate')) {
                echo "-------- [[ ERROR: Migration failed ]]\n";
                return false;
            }
            echo "------ Seeding... \n";
            if (!Artisan::call(
                'db:seed',
                [
                    '--class' => '\\Anibalealvarezs\\Projectbuilder\\Database\\Seeders\\PbMainSeeder'
                ]
            )) {
                echo "-------- [[ ERROR: Tables could not be seeded ]]\n";
                return false;
            }
        } catch (Exception $e) {
            echo "-------- [[ ERROR: ".$e->getMessage()." ]]\n";
            return false;
        }
        return true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */

    public function createLinks()
    {
        echo "------ Adding storage path...\n";
        if (!$this->addStoragePath()) {
            return false;
        }
        echo "------ Creating links...\n";
        if (!Artisan::call('storage:link')) {
            echo "------ [[ WARNING: Links could not be created ]]\n";
        }
    }
}
